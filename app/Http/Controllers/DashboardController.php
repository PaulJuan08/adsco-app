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
            // REMOVED ->with('course') from Topic since it no longer has course relationship
            'recentTopics' => Topic::latest()->take(3)->get(),
            // If Assignments and Quizzes still have course relationship, keep ->with('course')
            // If not, remove it from these too:
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
            'totalTopics' => Topic::count(), // Added total topics for registrar
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
        
        // Get upcoming assignments and quizzes for deadlines section
        $upcomingAssignments = Assignment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->where('due_date', '>', now())
        ->where('is_published', 1)
        ->orderBy('due_date', 'asc')
        ->take(3)
        ->get();
        
        $upcomingQuizzes = Quiz::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
        ->where('available_until', '>', now())
        ->where('is_published', 1)
        ->orderBy('available_until', 'asc')
        ->take(3)
        ->get();
        
        $data = [
            'myCourses' => $myCourses,
            'totalStudents' => Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                $query->select('id')->from('courses')->where('teacher_id', $teacherId);
            })->distinct('student_id')->count(),
            'totalTopics' => Topic::count(), // Added total topics for teacher
            'totalAssignments' => Assignment::whereHas('course', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->count(),
            'totalQuizzes' => Quiz::whereHas('course', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->count(),
            'recentEnrollments' => Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                $query->select('id')->from('courses')->where('teacher_id', $teacherId);
            })->with(['student', 'course'])->latest()->take(5)->get(),
            'pendingGrading' => 0, // You can implement this later
            'upcomingAssignments' => $upcomingAssignments,
            'upcomingQuizzes' => $upcomingQuizzes,
        ];
        
        return view('teacher.dashboard', $data);
    }

    private function studentDashboard()
    {
        $studentId = Auth::id();
        
        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('student_id', $studentId)
            ->where('status', 'active')
            ->pluck('course_id')
            ->toArray();
        
        // REMOVED course relationship from Topic since it no longer exists
        // Topics are now standalone, not tied to courses
        $studentTopics = Topic::where('is_published', 1)
            ->latest()
            ->take(5)
            ->get();
        
        // Check if Assignments and Quizzes still have course relationship
        // If they do, keep the whereIn clause. If not, remove it.
        $studentAssignments = Assignment::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', 1)
            ->with('course') // Keep this only if Assignment has course relationship
            ->latest()
            ->take(5)
            ->get();
        
        $studentQuizzes = Quiz::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', 1)
            ->with('course') // Keep this only if Quiz has course relationship
            ->latest()
            ->take(5)
            ->get();
        
        $data = [
            'enrolledCourses' => Enrollment::where('student_id', $studentId)
                ->with(['course.teacher'])
                ->where('status', 'active')
                ->get(),
            'completedCourses' => Enrollment::where('student_id', $studentId)
                ->where('status', 'completed')
                ->count(),
            'averageGrade' => Enrollment::where('student_id', $studentId)
                ->whereNotNull('grade')
                ->avg('grade') ?? 0,
            'totalTopics' => Topic::where('is_published', 1)->count(), // Added for student
            'totalAssignments' => Assignment::whereIn('course_id', $enrolledCourseIds)
                ->where('is_published', 1)
                ->count(),
            'totalQuizzes' => Quiz::whereIn('course_id', $enrolledCourseIds)
                ->where('is_published', 1)
                ->count(),
            'studentTopics' => $studentTopics,
            'studentAssignments' => $studentAssignments,
            'studentQuizzes' => $studentQuizzes,
        ];
        
        return view('student.dashboard', $data);
    }
}