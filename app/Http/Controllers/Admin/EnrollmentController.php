<?php
// app/Http/Controllers/Admin/EnrollmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\College;
use App\Models\Program;

class EnrollmentController extends Controller
{
    /**
     * Show enrollment management page
     */
    public function index(Request $request)
    {
        $courses = Course::select(['id', 'title', 'course_code', 'is_published', 'credits'])
            ->withCount('students')
            ->orderBy('title')
            ->get();
        
        $colleges = College::where('status', 1)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);
        
        // Get programs for initial load (optional)
        $programs = Program::orderBy('program_name')->get(['id', 'program_name', 'college_id']);
        
        // Get years
        $years = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
        
        // Get selected course if any
        $selectedCourse = null;
        if ($request->has('course_id')) {
            $selectedCourse = Course::find($request->course_id);
        }
        
        return view('admin.enrollments.index', compact('courses', 'colleges', 'programs', 'years', 'selectedCourse'));
    }
    
    /**
     * Get students based on filters (AJAX)
     */
    public function getStudents(Request $request)
    {
        $collegeId = $request->college_id;
        $programId = $request->program_id;
        $collegeYear = $request->college_year;
        $search = $request->search;
        $courseId = $request->course_id;
        
        $query = User::where('role', 4) // Students only
            ->select(['id', 'f_name', 'l_name', 'email', 'student_id', 'college_id', 'program_id', 'college_year'])
            ->with(['college:id,college_name', 'program:id,program_name']);
        
        // Apply college filter
        if ($collegeId && $collegeId !== 'all') {
            $query->where('college_id', $collegeId);
        }
        
        // Apply program filter
        if ($programId && $programId !== 'all') {
            $query->where('program_id', $programId);
        }
        
        // Apply year filter
        if ($collegeYear && $collegeYear !== 'all') {
            $query->where('college_year', $collegeYear);
        }
        
        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                  ->orWhere('l_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        // If course_id is provided, mark which students are already enrolled
        $enrolledStudentIds = [];
        if ($courseId) {
            $enrolledStudentIds = Enrollment::where('course_id', $courseId)
                ->pluck('student_id')
                ->toArray();
        }
        
        $students = $query->orderBy('f_name')->paginate(20);
        
        // Add enrolled status to each student
        $students->getCollection()->transform(function($student) use ($enrolledStudentIds) {
            $student->is_enrolled = in_array($student->id, $enrolledStudentIds);
            return $student;
        });
        
        return response()->json($students);
    }
    
    /**
     * Get enrolled students for a course (AJAX)
     */
    public function getEnrolledStudents($courseId)
    {
        try {
            $id = Crypt::decrypt(urldecode($courseId));
            
            $enrolledStudents = User::whereIn('id', function($query) use ($id) {
                    $query->select('student_id')
                        ->from('enrollments')
                        ->where('course_id', $id);
                })
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id'])
                ->with(['college:id,college_name', 'program:id,program_name'])
                ->orderBy('f_name')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->full_name,
                        'email' => $student->email,
                        'student_id' => $student->student_id,
                        'college' => $student->college->college_name ?? 'N/A',
                        'program' => $student->program->program_name ?? 'N/A'
                    ];
                });
            
            return response()->json($enrolledStudents);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Enroll students in a course
     */
    public function enroll(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:users,id'
            ]);
            
            $courseId = $request->course_id;
            $studentIds = $request->student_ids;
            
            // Get already enrolled students
            $existingEnrollments = Enrollment::where('course_id', $courseId)
                ->whereIn('student_id', $studentIds)
                ->pluck('student_id')
                ->toArray();
            
            // Filter out already enrolled students
            $newStudentIds = array_diff($studentIds, $existingEnrollments);
            
            if (empty($newStudentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected students are already enrolled in this course.'
                ]);
            }
            
            // Create enrollments
            $enrollments = [];
            foreach ($newStudentIds as $studentId) {
                $enrollments[] = [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            DB::table('enrollments')->insert($enrollments);
            
            // Clear caches
            $this->clearEnrollmentCaches($courseId, $newStudentIds);
            
            // Get enrolled students for response
            $enrolledStudents = User::whereIn('id', $newStudentIds)
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id'])
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->full_name,
                        'email' => $student->email,
                        'student_id' => $student->student_id
                    ];
                });
            
            return response()->json([
                'success' => true,
                'message' => count($newStudentIds) . ' student(s) enrolled successfully.',
                'enrolled_students' => $enrolledStudents
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll students: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove a student from a course
     */
    public function remove(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'student_id' => 'required|exists:users,id'
            ]);
            
            $courseId = $request->course_id;
            $studentId = $request->student_id;
            
            // Check if enrollment exists
            $enrollment = Enrollment::where('course_id', $courseId)
                ->where('student_id', $studentId)
                ->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not enrolled in this course.'
                ]);
            }
            
            $enrollment->delete();
            
            // Clear caches
            $this->clearEnrollmentCaches($courseId, [$studentId]);
            
            return response()->json([
                'success' => true,
                'message' => 'Student removed from course successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove student: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk enroll students from CSV
     */
    public function bulkEnroll(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'csv_file' => 'required|file|mimes:csv,txt|max:2048'
            ]);
            
            $courseId = $request->course_id;
            $file = $request->file('csv_file');
            
            // Parse CSV
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $headers = array_shift($csvData); // Remove headers
            
            // Find student_id column index
            $studentIdIndex = array_search('student_id', $headers);
            if ($studentIdIndex === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV must contain a "student_id" column.'
                ]);
            }
            
            $studentIds = [];
            $notFound = [];
            
            foreach ($csvData as $row) {
                if (isset($row[$studentIdIndex]) && !empty($row[$studentIdIndex])) {
                    $studentId = trim($row[$studentIdIndex]);
                    
                    // Find user by student_id
                    $user = User::where('role', 4)
                        ->where('student_id', $studentId)
                        ->first();
                    
                    if ($user) {
                        $studentIds[] = $user->id;
                    } else {
                        $notFound[] = $studentId;
                    }
                }
            }
            
            if (empty($studentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid student IDs found in CSV.'
                ]);
            }
            
            // Get already enrolled students
            $existingEnrollments = Enrollment::where('course_id', $courseId)
                ->whereIn('student_id', $studentIds)
                ->pluck('student_id')
                ->toArray();
            
            // Filter out already enrolled students
            $newStudentIds = array_diff($studentIds, $existingEnrollments);
            
            if (empty($newStudentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All students in CSV are already enrolled.'
                ]);
            }
            
            // Create enrollments
            $enrollments = [];
            foreach ($newStudentIds as $studentId) {
                $enrollments[] = [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            DB::table('enrollments')->insert($enrollments);
            
            // Clear caches
            $this->clearEnrollmentCaches($courseId, $newStudentIds);
            
            $message = count($newStudentIds) . ' student(s) enrolled successfully.';
            if (!empty($notFound)) {
                $message .= ' ' . count($notFound) . ' student ID(s) not found: ' . implode(', ', array_slice($notFound, 0, 5));
                if (count($notFound) > 5) {
                    $message .= ' and ' . (count($notFound) - 5) . ' more.';
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'enrolled_count' => count($newStudentIds),
                'not_found' => $notFound
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk enroll: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get programs by college (AJAX for filter)
     */
    public function getProgramsByCollege($collegeId)
    {
        try {
            $programs = Program::where('college_id', $collegeId)
                ->orderBy('program_name')
                ->get(['id', 'program_name', 'program_code']);
            
            return response()->json($programs);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Clear enrollment-related caches
     */
    private function clearEnrollmentCaches($courseId, $studentIds)
    {
        // Clear course show cache
        Cache::forget('course_show_' . $courseId);
        Cache::forget('admin_course_show_' . $courseId);
        
        // Clear student caches
        foreach ($studentIds as $studentId) {
            // Clear student dashboard
            Cache::forget('student_dashboard_' . $studentId);
            
            // Clear student courses index pages
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
            }
            
            // Clear student course show
            Cache::forget('student_course_show_' . $courseId);
            
            // Clear student overall stats
            Cache::forget('student_overall_stats_' . $studentId);
        }
        
        // Clear admin dashboard
        Cache::forget('admin_dashboard_' . auth()->id());
    }
    
    /**
     * Show enrollment for a specific student (from user profile)
     */
    public function studentEnrollments($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $student = User::with(['college', 'program'])->findOrFail($id);
            
            if ($student->role != 4) {
                abort(404, 'User is not a student.');
            }
            
            // Get enrolled courses
            $enrolledCourses = Enrollment::where('student_id', $id)
                ->with('course')
                ->orderBy('enrolled_at', 'desc')
                ->get();
            
            // Get available courses (published and not enrolled)
            $enrolledCourseIds = $enrolledCourses->pluck('course_id')->toArray();
            
            $availableCourses = Course::where('is_published', true)
                ->whereNotIn('id', $enrolledCourseIds)
                ->orderBy('title')
                ->get(['id', 'title', 'course_code', 'credits', 'description']);
            
            return view('admin.enrollments.student', compact('student', 'enrolledCourses', 'availableCourses'));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to load student enrollments: ' . $e->getMessage());
        }
    }
}