<?php
// app/Http/Controllers/Teacher/EnrollmentController.php

namespace App\Http\Controllers\Teacher;

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
     * Show enrollment management page (teacher's courses only)
     */
    public function index(Request $request)
    {
        $teacherId = auth()->id();

        $courses = Course::select(['id', 'title', 'course_code', 'is_published', 'credits', 'description'])
            ->where('teacher_id', $teacherId) // Scope to teacher's own courses
            ->withCount('students')
            ->orderBy('title')
            ->get();

        // Encrypt course IDs for frontend
        $courses->each(function ($course) {
            $course->encrypted_id = Crypt::encrypt($course->id);
        });

        $colleges = College::where('status', 1)
            ->orderBy('college_name')
            ->get(['id', 'college_name']);

        $years = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

        $selectedCourse = null;
        if ($request->has('course_id')) {
            $selectedCourse = Course::where('teacher_id', $teacherId)
                ->find($request->course_id);
            if ($selectedCourse) {
                $selectedCourse->encrypted_id = Crypt::encrypt($selectedCourse->id);
            }
        }

        return view('teacher.enrollments.index', compact('courses', 'colleges', 'years', 'selectedCourse'));
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
     * Verify the decrypted course belongs to the logged-in teacher
     */
    private function authorizeTeacherCourse($courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('teacher_id', auth()->id())
            ->first();

        if (!$course) {
            abort(403, 'You do not have access to this course.');
        }

        return $course;
    }

    /**
     * Get students based on filters (AJAX)
     */
    public function getStudents(Request $request)
    {
        try {
            $collegeId    = $request->college_id;
            $programId    = $request->program_id;
            $collegeYear  = $request->college_year;
            $search       = $request->search;
            $encryptedCourseId = $request->course_id;
            $studentId    = $request->student_id;
            $name         = $request->name;

            $query = User::where('role', 4)
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id', 'college_id', 'program_id', 'college_year'])
                ->with(['college:id,college_name', 'program:id,program_name']);

            if ($studentId) {
                $query->where('student_id', 'like', "%{$studentId}%");
            }

            if ($name) {
                $query->where(function ($q) use ($name) {
                    $q->where('f_name', 'like', "%{$name}%")
                      ->orWhere('l_name', 'like', "%{$name}%");
                });
            }

            if ($collegeId && $collegeId !== 'all') {
                $query->where('college_id', $collegeId);
            }

            if ($programId && $programId !== 'all') {
                $query->where('program_id', $programId);
            }

            if ($collegeYear && $collegeYear !== 'all') {
                $query->where('college_year', $collegeYear);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
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
                $this->authorizeTeacherCourse($actualCourseId); // Ensure ownership

                $enrolledStudentIds = Enrollment::where('course_id', $actualCourseId)
                    ->pluck('student_id')
                    ->toArray();
            }

            $students = $query->orderBy('f_name')->paginate(20);

            $students->getCollection()->transform(function ($student) use ($enrolledStudentIds) {
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
            $this->authorizeTeacherCourse($actualCourseId);

            $enrolledStudents = User::whereIn('id', function ($query) use ($actualCourseId) {
                    $query->select('student_id')
                        ->from('enrollments')
                        ->where('course_id', $actualCourseId);
                })
                ->select(['id', 'f_name', 'l_name', 'email', 'student_id'])
                ->orderBy('f_name')
                ->get()
                ->map(function ($student) {
                    return [
                        'id'         => $student->id,
                        'name'       => $student->f_name . ' ' . $student->l_name,
                        'email'      => $student->email,
                        'student_id' => $student->student_id,
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
            $this->authorizeTeacherCourse($actualCourseId);

            $studentIds = Enrollment::where('course_id', $actualCourseId)
                ->pluck('student_id')
                ->toArray();

            return response()->json(['student_ids' => $studentIds]);

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
                'course_id'    => 'required',
                'student_ids'  => 'required|array',
                'student_ids.*' => 'exists:users,id',
            ]);

            $actualCourseId = $this->decryptId($request->course_id);
            $this->authorizeTeacherCourse($actualCourseId);

            $studentIds = $request->student_ids;

            $existingEnrollments = Enrollment::where('course_id', $actualCourseId)
                ->whereIn('student_id', $studentIds)
                ->pluck('student_id')
                ->toArray();

            $newStudentIds = array_diff($studentIds, $existingEnrollments);

            if (empty($newStudentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'All selected students are already enrolled in this course.',
                ]);
            }

            $enrollments = [];
            foreach ($newStudentIds as $studentId) {
                $enrollments[] = [
                    'student_id'  => $studentId,
                    'course_id'   => $actualCourseId,
                    'enrolled_at' => now(),
                    'status'      => 'active',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            DB::table('enrollments')->insert($enrollments);

            $this->clearEnrollmentCaches($actualCourseId, $newStudentIds);

            return response()->json([
                'success' => true,
                'message' => count($newStudentIds) . ' student(s) enrolled successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll students: ' . $e->getMessage(),
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
                'course_id'  => 'required',
                'student_id' => 'required|exists:users,id',
            ]);

            $actualCourseId = $this->decryptId($request->course_id);
            $this->authorizeTeacherCourse($actualCourseId);

            $studentId = $request->student_id;

            $enrollment = Enrollment::where('course_id', $actualCourseId)
                ->where('student_id', $studentId)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not enrolled in this course.',
                ]);
            }

            $enrollment->delete();

            $this->clearEnrollmentCaches($actualCourseId, [$studentId]);

            return response()->json([
                'success' => true,
                'message' => 'Student removed from course successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove student: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get programs by college (AJAX for filter)
     */
    public function getProgramsByCollege($collegeId)
    {
        try {
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
        Cache::forget('course_show_' . $courseId);
        Cache::forget('admin_course_show_' . $courseId);
        Cache::forget('teacher_course_show_' . $courseId);

        foreach ($studentIds as $studentId) {
            Cache::forget('student_dashboard_' . $studentId);

            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
            }

            Cache::forget('student_course_show_' . $courseId);
            Cache::forget('student_overall_stats_' . $studentId);
        }

        Cache::forget('teacher_dashboard_' . auth()->id());
    }
}