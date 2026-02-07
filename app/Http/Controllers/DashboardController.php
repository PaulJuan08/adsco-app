<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Topic;
use App\Models\Assignment;
use App\Models\Quiz;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirect based on role
        switch ($user->role) {
            case 1: // Admin
                return $this->adminDashboard();
            case 2: // Registrar
                return $this->registrarDashboard();
            case 3: // Teacher
                return $this->teacherDashboard();
            case 4: // Student
                return $this->studentDashboard();
            default:
                abort(403, 'Invalid role');
        }
    }
    
    private function adminDashboard()
    {
        $data = [
            'totalUsers' => User::count(),
            'pendingUsers' => User::where('is_approved', false)->latest()->take(5)->get(),
            'pendingApprovals' => User::where('is_approved', false)->count(),
            'totalCourses' => Course::count(),
            'activeEnrollments' => Enrollment::where('status', 'active')->count(),
            'todayLogins' => User::whereDate('last_login_at', today())->count(),
            'totalQuizzes' => Quiz::count(),
            'totalTopics' => Topic::count(),
            'totalAssignments' => Assignment::count(),
            'recentTopics' => Topic::latest()->take(3)->get(),
            'recentAssignments' => Assignment::latest()->take(3)->get(),
            'recentQuizzes' => Quiz::latest()->take(3)->get(),
        ];
        
        return view('admin.dashboard', $data);
    }
    
    private function registrarDashboard()
    {
        $data = [
            'pendingTeachers' => User::where('role', 3)->where('is_approved', false)->get(),
            'pendingStudents' => User::where('role', 4)->where('is_approved', false)->get(),
            'totalTeachers' => User::where('role', 3)->count(),
            'totalStudents' => User::where('role', 4)->count(),
            'totalPending' => User::whereIn('role', [3, 4])->where('is_approved', false)->count(),
            'totalApproved' => User::whereIn('role', [3, 4])->where('is_approved', true)->count(),
            'totalTopics' => Topic::count(),
        ];
        
        return view('registrar.dashboard', $data)->with('layout', 'registrar');
    }
    
    private function teacherDashboard()
    {
        $teacherId = Auth::id();
        
        // Get teacher's courses
        $myCourses = Course::where('teacher_id', $teacherId)->get();
        
        // Calculate enrollment count for each course manually
        foreach ($myCourses as $course) {
            $course->enrollments_count = Enrollment::where('course_id', $course->id)
                ->where('status', 'active')
                ->count();
        }
        
        // Get upcoming assignments from teacher's courses
        $upcomingAssignments = Assignment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->where('due_date', '>', now())
        ->where('is_published', 1)
        ->orderBy('due_date', 'asc')
        ->take(3)
        ->get();
        
        // FIXED: Quizzes don't have course relationship in your model
        // If quizzes are meant to be standalone (not tied to courses), show all quizzes
        // Or if they should be tied to courses, you need to add course_id to quizzes table
        $upcomingQuizzes = Quiz::where('available_until', '>', now())
            ->where('is_published', 1)
            ->orderBy('available_until', 'asc')
            ->take(3)
            ->get();
        
        // Get all quizzes count (since they're not tied to courses)
        $totalQuizzes = Quiz::count();
        
        // Get assignments count from teacher's courses
        $totalAssignments = Assignment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();
        
        $data = [
            'myCourses' => $myCourses,
            'totalStudents' => Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                $query->select('id')->from('courses')->where('teacher_id', $teacherId);
            })->where('status', 'active')->distinct('student_id')->count(),
            'totalTopics' => Topic::count(),
            'totalAssignments' => $totalAssignments,
            'totalQuizzes' => $totalQuizzes, // Changed: all quizzes since not tied to courses
            'recentEnrollments' => Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                $query->select('id')->from('courses')->where('teacher_id', $teacherId);
            })->with(['student', 'course'])->latest()->take(5)->get(),
            'pendingGrading' => 0,
            'upcomingAssignments' => $upcomingAssignments,
            'upcomingQuizzes' => $upcomingQuizzes,
        ];
        
        return view('teacher.dashboard', $data);
    }

    private function studentDashboard()
    {
        $studentId = Auth::id();
        
        // Use CourseController to get stats
        $courseController = new \App\Http\Controllers\Student\CourseController();
        $overallStats = $courseController->getOverallStats($studentId);
        
        // Get enrolled courses
        $enrolledCourses = Enrollment::where('student_id', $studentId)
            ->with(['course.teacher'])
            ->where('status', 'active')
            ->take(4)
            ->get();
        
        // Calculate progress for each course
        foreach ($enrolledCourses as $enrollment) {
            $progress = $enrollment->course->getStudentProgress($studentId);
            $enrollment->course->progress = $progress;
            $enrollment->progress = $progress;
        }
        
        // Get available courses
        $availableCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourses->pluck('course_id')->toArray())
            ->with('teacher')
            ->orderBy('title')
            ->limit(3)
            ->get();
        
        // Get available quizzes
        $availableQuizzes = Quiz::where('is_published', true)
            ->where(function($query) {
                $query->whereNull('available_until')
                    ->orWhere('available_until', '>', now());
            })
            ->where(function($query) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->orderBy('available_from', 'desc')
            ->limit(5)
            ->get();
        
        // Get upcoming quizzes
        $upcomingQuizzes = Quiz::where('is_published', true)
            ->where('available_from', '>', now())
            ->where('available_from', '<=', now()->addDays(7))
            ->orderBy('available_from')
            ->limit(3)
            ->get();
        
        // Get recent topics
        $recentTopics = Topic::where('is_published', 1)
            ->latest()
            ->take(5)
            ->get();
        
        // Get assignments for enrolled courses
        $enrolledCourseIds = $enrolledCourses->pluck('course_id')->toArray();
        $studentAssignments = Assignment::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', 1)
            ->with('course')
            ->latest()
            ->take(5)
            ->get();
        
        $data = [
            // Progress stats
            'stats' => $overallStats,
            
            // Courses
            'enrolledCourses' => $enrolledCourses,
            'availableCourses' => $availableCourses,
            'completedCourses' => $overallStats['completed_courses'],
            'averageGrade' => $overallStats['average_grade'],
            'totalEnrolled' => $overallStats['total_courses'],
            
            // Quizzes
            'availableQuizzes' => $availableQuizzes,
            'availableQuizzesCount' => $availableQuizzes->count(),
            'upcomingQuizzes' => $upcomingQuizzes,
            
            // Other data
            'recentAttendance' => collect([]),
            'studentTopics' => $recentTopics,
            'studentAssignments' => $studentAssignments,
            'studentQuizzes' => $availableQuizzes,
            'totalTopics' => $overallStats['total_topics'],
            'totalAssignments' => Assignment::whereIn('course_id', $enrolledCourseIds)
                ->where('is_published', 1)->count(),
            'totalQuizzes' => Quiz::where('is_published', 1)->count(),
        ];
        
        return view('student.dashboard', $data);
    }
}