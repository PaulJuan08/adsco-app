<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\AuditLog;

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
            'pendingUsers' => User::where('is_approved', false)->get(),
            'pendingApprovals' => User::where('is_approved', false)->count(),
            'totalCourses' => Course::count(),
            'activeEnrollments' => Enrollment::where('status', 'active')->count(),
            'todayLogins' => Attendance::whereDate('date', today())->count(),
            'totalQuizzes' => \App\Models\Quiz::count(),
            'recentActivities' => AuditLog::latest()->take(5)->get()
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
        ];
        
        return view('registrar.dashboard', $data)->with('layout', 'registrar');
    }
    
    private function teacherDashboard()
    {
        $teacherId = Auth::id();
        
        $data = [
            'myCourses' => Course::where('teacher_id', $teacherId)->get(),
            'totalStudents' => Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                $query->select('id')->from('courses')->where('teacher_id', $teacherId);
            })->distinct('student_id')->count(),
            'recentEnrollments' => Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                $query->select('id')->from('courses')->where('teacher_id', $teacherId);
            })->latest()->take(5)->get()
        ];
        
        return view('teacher.dashboard', $data);
    }
    
    private function studentDashboard()
    {
        $studentId = Auth::id();
        
        $data = [
            'enrolledCourses' => Enrollment::where('student_id', $studentId)
                ->with('course')
                ->where('status', 'active')
                ->get(),
            'completedCourses' => Enrollment::where('student_id', $studentId)
                ->where('status', 'completed')
                ->count(),
            'averageGrade' => Enrollment::where('student_id', $studentId)
                ->whereNotNull('grade')
                ->avg('grade'),
            'attendance' => Attendance::where('user_id', $studentId)
                ->whereDate('date', '>=', now()->subDays(30))
                ->orderBy('date', 'desc')
                ->get()
        ];
        
        return view('student.dashboard', $data);
    }
}