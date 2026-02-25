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
        $courses = Course::select(['id', 'title', 'course_code', 'is_published', 'credits', 'description'])
            ->withCount('students')
            ->orderBy('title')
            ->get();
        
        // Encrypt course IDs for frontend
        $courses->each(function($course) {
            $course->encrypted_id = Crypt::encrypt($course->id);
        });
        
        $colleges = College::where('status', 1)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);
        
        // Get years
        $years = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
        
        // Get selected course if any
        $selectedCourse = null;
        if ($request->has('course_id')) {
            $selectedCourse = Course::find($request->course_id);
            if ($selectedCourse) {
                $selectedCourse->encrypted_id = Crypt::encrypt($selectedCourse->id);
            }
        }
        
        return view('admin.enrollments.index', compact('courses', 'colleges', 'years', 'selectedCourse'));
    }
    
    /**
     * Helper function to decrypt ID
     */
    private function decryptId($encryptedId)
    {
        try {
            return Crypt::decrypt(urldecode($encryptedId));
        } catch (\Exception $e) {
            abort(404, 'Invalid ID');
        }
    }
    
    /**
     * Get students based on filters (AJAX)
     */
    public function getStudents(Request $request)
    {
        try {
            $collegeId = $request->college_id;
            $programId = $request->program_id;
            $collegeYear = $request->college_year;
            $search = $request->search;
            $encryptedCourseId = $request->course_id;
            $studentId = $request->student_id;
            $name = $request->name;
            
            $query = User::where('role', 4) // Students only
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id', 'college_id', 'program_id', 'college_year'])
                ->with(['college:id,college_name', 'program:id,program_name']);
            
            // Apply student ID filter
            if ($studentId) {
                $query->where('student_id', 'like', "%{$studentId}%");
            }
            
            // Apply name filter
            if ($name) {
                $query->where(function($q) use ($name) {
                    $q->where('f_name', 'like', "%{$name}%")
                      ->orWhere('l_name', 'like', "%{$name}%");
                });
            }
            
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
            
            // Apply search (backward compatibility)
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
            if ($encryptedCourseId) {
                $actualCourseId = $this->decryptId($encryptedCourseId);
                $enrolledStudentIds = Enrollment::where('course_id', $actualCourseId)
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
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get enrolled students for a course (AJAX)
     */
    public function getEnrolledStudents($encryptedCourseId)
    {
        try {
            $actualCourseId = $this->decryptId($encryptedCourseId);
            
            $enrolledStudents = User::whereIn('id', function($query) use ($actualCourseId) {
                    $query->select('student_id')
                        ->from('enrollments')
                        ->where('course_id', $actualCourseId);
                })
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id'])
                ->orderBy('f_name')
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->f_name . ' ' . $student->l_name,
                        'email' => $student->email,
                        'student_id' => $student->student_id
                    ];
                });
            
            return response()->json($enrolledStudents);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get enrolled student IDs for a course (AJAX)
     */
    public function getEnrolledStudentIds($encryptedCourseId)
    {
        try {
            $actualCourseId = $this->decryptId($encryptedCourseId);
            
            $studentIds = Enrollment::where('course_id', $actualCourseId)
                ->pluck('student_id')
                ->toArray();
            
            return response()->json([
                'student_ids' => $studentIds
            ]);
            
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
                'course_id' => 'required',
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:users,id'
            ]);
            
            $actualCourseId = $this->decryptId($request->course_id);
            $studentIds = $request->student_ids;
            
            // Get already enrolled students
            $existingEnrollments = Enrollment::where('course_id', $actualCourseId)
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
                    'course_id' => $actualCourseId,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            DB::table('enrollments')->insert($enrollments);
            
            // Clear caches
            $this->clearEnrollmentCaches($actualCourseId, $newStudentIds);
            
            return response()->json([
                'success' => true,
                'message' => count($newStudentIds) . ' student(s) enrolled successfully.'
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
                'course_id' => 'required',
                'student_id' => 'required|exists:users,id'
            ]);
            
            $actualCourseId = $this->decryptId($request->course_id);
            $studentId = $request->student_id;
            
            // Check if enrollment exists
            $enrollment = Enrollment::where('course_id', $actualCourseId)
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
            $this->clearEnrollmentCaches($actualCourseId, [$studentId]);
            
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
     * Get programs by college (AJAX for filter)
     */
    public function getProgramsByCollege($collegeId)
    {
        try {
            // College ID might be encrypted or raw
            $actualCollegeId = is_numeric($collegeId) ? $collegeId : $this->decryptId($collegeId);
            
            $programs = Program::where('college_id', $actualCollegeId)
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
            Cache::forget('student_dashboard_' . $studentId);
            
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
            }
            
            Cache::forget('student_course_show_' . $courseId);
            Cache::forget('student_overall_stats_' . $studentId);
        }
        
        Cache::forget('admin_dashboard_' . auth()->id());
    }
}