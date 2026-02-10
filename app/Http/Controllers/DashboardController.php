<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        // Cache key specific to admin dashboard
        $cacheKey = 'admin_dashboard_' . auth()->id();
        
        // Cache for 5 minutes (300 seconds)
        $data = Cache::remember($cacheKey, 300, function() {
            // Get all counts in optimized queries
            $userCounts = User::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN role = 1 THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN role = 2 THEN 1 ELSE 0 END) as registrars,
                SUM(CASE WHEN role = 3 THEN 1 ELSE 0 END) as teachers,
                SUM(CASE WHEN role = 4 THEN 1 ELSE 0 END) as students
            ')->first();
            
            // Get pending users (limited to 5)
            $pendingUsers = User::where('is_approved', false)
                ->select(['id', 'f_name', 'l_name', 'email', 'role', 'created_at'])
                ->latest()
                ->take(5)
                ->get();
            
            // Get counts with single queries
            $totalCourses = Course::count();
            $totalQuizzes = Quiz::count();
            $totalTopics = Topic::count();
            $totalAssignments = Assignment::count();
            
            // Get active enrollments
            $activeEnrollments = Enrollment::where('status', 'active')->count();
            
            // Today's logins - cache separately since it changes more frequently
            $todayLoginsCacheKey = 'today_logins_' . date('Y-m-d');
            $todayLogins = Cache::remember($todayLoginsCacheKey, 60, function() {
                return User::whereDate('last_login_at', today())->count();
            });
            
            // Get recent content with eager loading
            $recentTopics = Topic::with('course')
                ->select(['id', 'title', 'description', 'course_id', 'created_at'])
                ->latest()
                ->take(3)
                ->get();
            
            $recentAssignments = Assignment::with('course')
                ->select(['id', 'title', 'course_id', 'created_at'])
                ->latest()
                ->take(3)
                ->get();
            
            $recentQuizzes = Quiz::with('course')
                ->select(['id', 'title', 'course_id', 'created_at'])
                ->latest()
                ->take(3)
                ->get();
            
            return [
                'totalUsers' => $userCounts->total ?? 0,
                'pendingUsers' => $pendingUsers,
                'pendingApprovals' => $userCounts->pending ?? 0,
                'totalCourses' => $totalCourses,
                'activeEnrollments' => $activeEnrollments,
                'todayLogins' => $todayLogins,
                'totalQuizzes' => $totalQuizzes,
                'totalTopics' => $totalTopics,
                'totalAssignments' => $totalAssignments,
                'recentTopics' => $recentTopics,
                'recentAssignments' => $recentAssignments,
                'recentQuizzes' => $recentQuizzes,
                'userStats' => [
                    'admins' => $userCounts->admins ?? 0,
                    'registrars' => $userCounts->registrars ?? 0,
                    'teachers' => $userCounts->teachers ?? 0,
                    'students' => $userCounts->students ?? 0,
                ]
            ];
        });
        
        return view('admin.dashboard', $data);
    }
    
    private function registrarDashboard()
    {
        $cacheKey = 'registrar_dashboard_' . auth()->id();
        
        $data = Cache::remember($cacheKey, 300, function() {
            // Get user counts in one query
            $teacherStudentCounts = User::selectRaw('
                SUM(CASE WHEN role = 3 AND is_approved = 0 THEN 1 ELSE 0 END) as pending_teachers,
                SUM(CASE WHEN role = 4 AND is_approved = 0 THEN 1 ELSE 0 END) as pending_students,
                SUM(CASE WHEN role = 3 AND is_approved = 1 THEN 1 ELSE 0 END) as approved_teachers,
                SUM(CASE WHEN role = 4 AND is_approved = 1 THEN 1 ELSE 0 END) as approved_students,
                SUM(CASE WHEN role IN (3, 4) AND is_approved = 0 THEN 1 ELSE 0 END) as total_pending,
                SUM(CASE WHEN role IN (3, 4) AND is_approved = 1 THEN 1 ELSE 0 END) as total_approved
            ')->first();
            
            // Get pending teachers and students separately (small datasets)
            $pendingTeachers = User::where('role', 3)
                ->where('is_approved', false)
                ->select(['id', 'f_name', 'l_name', 'email', 'created_at'])
                ->get();
            
            $pendingStudents = User::where('role', 4)
                ->where('is_approved', false)
                ->select(['id', 'f_name', 'l_name', 'email', 'created_at'])
                ->get();
            
            $totalTopics = Topic::count();
            
            return [
                'pendingTeachers' => $pendingTeachers,
                'pendingStudents' => $pendingStudents,
                'totalTeachers' => $teacherStudentCounts->approved_teachers ?? 0,
                'totalStudents' => $teacherStudentCounts->approved_students ?? 0,
                'totalPending' => $teacherStudentCounts->total_pending ?? 0,
                'totalApproved' => $teacherStudentCounts->total_approved ?? 0,
                'totalTopics' => $totalTopics,
            ];
        });
        
        return view('registrar.dashboard', $data)->with('layout', 'registrar');
    }
    
    private function teacherDashboard()
    {
        $teacherId = Auth::id();
        $cacheKey = 'teacher_dashboard_' . $teacherId;
        
        $data = Cache::remember($cacheKey, 300, function() use ($teacherId) {
            // Get teacher's courses with counts
            $myCourses = Course::where('teacher_id', $teacherId)
                ->withCount(['enrollments as active_enrollments' => function($query) {
                    $query->where('status', 'active');
                }])
                ->get();
            
            // Get total students across all courses
            $totalStudents = Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                    $query->select('id')->from('courses')->where('teacher_id', $teacherId);
                })
                ->where('status', 'active')
                ->distinct('student_id')
                ->count();
            
            // Get upcoming assignments
            $upcomingAssignments = Assignment::whereHas('course', function($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                })
                ->where('due_date', '>', now())
                ->where('is_published', 1)
                ->with('course')
                ->orderBy('due_date', 'asc')
                ->take(3)
                ->get(['id', 'title', 'course_id', 'due_date', 'created_at']);
            
            // Get upcoming quizzes
            $upcomingQuizzes = Quiz::where('available_until', '>', now())
                ->where('is_published', 1)
                ->orderBy('available_until', 'asc')
                ->take(3)
                ->get(['id', 'title', 'available_until', 'created_at']);
            
            // Get total counts
            $totalAssignments = Assignment::whereHas('course', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->count();
            
            $totalTopics = Topic::count();
            $totalQuizzes = Quiz::count();
            
            // Get recent enrollments
            $recentEnrollments = Enrollment::whereIn('course_id', function($query) use ($teacherId) {
                    $query->select('id')->from('courses')->where('teacher_id', $teacherId);
                })
                ->with(['student:id,f_name,l_name', 'course:id,title'])
                ->select(['id', 'student_id', 'course_id', 'created_at'])
                ->latest()
                ->take(5)
                ->get();
            
            return [
                'myCourses' => $myCourses,
                'totalStudents' => $totalStudents,
                'totalTopics' => $totalTopics,
                'totalAssignments' => $totalAssignments,
                'totalQuizzes' => $totalQuizzes,
                'recentEnrollments' => $recentEnrollments,
                'pendingGrading' => 0, // You can implement this later
                'upcomingAssignments' => $upcomingAssignments,
                'upcomingQuizzes' => $upcomingQuizzes,
            ];
        });
        
        return view('teacher.dashboard', $data);
    }
    
    private function studentDashboard()
    {
        $studentId = Auth::id();
        $cacheKey = 'student_dashboard_' . $studentId;
        
        $data = Cache::remember($cacheKey, 180, function() use ($studentId) {
            // Get enrolled courses with progress - REMOVE quizzes relationship
            $enrolledCourses = Enrollment::where('student_id', $studentId)
                ->where('status', 'active')
                ->with([
                    'course.teacher:id,f_name,l_name',
                    'course' => function($query) {
                        // Only count topics since quizzes might not be related to courses
                        $query->withCount(['topics']);
                    }
                ])
                ->take(4)
                ->get();
            
            // Calculate progress for each course
            $totalProgress = 0;
            $completedCourses = 0;
            
            foreach ($enrolledCourses as $enrollment) {
                // Check if course exists and has topics_count
                if ($enrollment->course && isset($enrollment->course->topics_count)) {
                    // Simple progress calculation - adjust based on your logic
                    $topicsCompleted = $enrollment->course->topics_count > 0 
                        ? rand(0, $enrollment->course->topics_count) // Replace with actual logic
                        : 0;
                    
                    $progress = $enrollment->course->topics_count > 0 
                        ? ($topicsCompleted / $enrollment->course->topics_count) * 100 
                        : 0;
                    
                    $enrollment->progress = min(100, $progress);
                    $enrollment->course->progress = $enrollment->progress;
                    
                    if ($enrollment->progress >= 100) {
                        $completedCourses++;
                    }
                    
                    $totalProgress += $enrollment->progress;
                } else {
                    // Default values if course not found
                    $enrollment->progress = 0;
                    if ($enrollment->course) {
                        $enrollment->course->progress = 0;
                    }
                }
            }
            
            $averageProgress = count($enrolledCourses) > 0 
                ? round($totalProgress / count($enrolledCourses), 1) 
                : 0;
            
            // Get available courses
            $enrolledCourseIds = $enrolledCourses->pluck('course_id')->toArray();
            $availableCourses = Course::where('is_published', true)
                ->whereNotIn('id', $enrolledCourseIds)
                ->with('teacher:id,f_name,l_name')
                ->orderBy('title')
                ->limit(3)
                ->get(['id', 'title', 'description', 'teacher_id']);
            
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
                ->get(['id', 'title', 'description', 'available_from', 'available_until']);
            
            // Get upcoming quizzes
            $upcomingQuizzes = Quiz::where('is_published', true)
                ->where('available_from', '>', now())
                ->where('available_from', '<=', now()->addDays(7))
                ->orderBy('available_from')
                ->limit(3)
                ->get(['id', 'title', 'available_from', 'available_until']);
            
            // Get assignments for enrolled courses
            $studentAssignments = Assignment::whereIn('course_id', $enrolledCourseIds)
                ->where('is_published', 1)
                ->with('course:id,title')
                ->latest()
                ->take(5)
                ->get(['id', 'title', 'description', 'course_id', 'due_date']);
            
            // Get recent topics
            $recentTopics = Topic::where('is_published', 1)
                ->latest()
                ->take(5)
                ->get(['id', 'title', 'description', 'created_at']);
            
            // Get assignments count for enrolled courses
            $totalAssignments = Assignment::whereIn('course_id', $enrolledCourseIds)
                ->where('is_published', 1)
                ->count();
            
            return [
                // Progress stats
                'stats' => [
                    'completed_courses' => $completedCourses,
                    'total_courses' => count($enrolledCourses),
                    'average_grade' => $averageProgress,
                    'total_topics' => Topic::count(),
                    'average_progress' => $averageProgress,
                ],
                
                // Courses
                'enrolledCourses' => $enrolledCourses,
                'availableCourses' => $availableCourses,
                'completedCourses' => $completedCourses,
                'averageGrade' => $averageProgress,
                'totalEnrolled' => count($enrolledCourses),
                
                // Quizzes
                'availableQuizzes' => $availableQuizzes,
                'availableQuizzesCount' => $availableQuizzes->count(),
                'upcomingQuizzes' => $upcomingQuizzes,
                
                // Other data
                'recentAttendance' => collect([]),
                'studentTopics' => $recentTopics,
                'studentAssignments' => $studentAssignments,
                'studentQuizzes' => $availableQuizzes,
                'totalTopics' => $recentTopics->count(),
                'totalAssignments' => $totalAssignments,
                'totalQuizzes' => Quiz::where('is_published', 1)->count(),
            ];
        });
        
        return view('student.dashboard', $data);
    }
    
    /**
     * Clear dashboard cache for current user
     * Useful when data changes and needs immediate refresh
     */
    public function clearCache()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Clear specific dashboard cache
        switch ($user->role) {
            case 1:
                Cache::forget('admin_dashboard_' . $userId);
                break;
            case 2:
                Cache::forget('registrar_dashboard_' . $userId);
                break;
            case 3:
                Cache::forget('teacher_dashboard_' . $userId);
                break;
            case 4:
                Cache::forget('student_dashboard_' . $userId);
                break;
        }
        
        // Also clear today's logins cache
        Cache::forget('today_logins_' . date('Y-m-d'));
        
        return redirect()->back()->with('success', 'Dashboard cache cleared successfully.');
    }
}