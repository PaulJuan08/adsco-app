<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Topic;
use App\Models\Assignment;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache key specific to admin dashboard - include user ID
        $cacheKey = 'admin_dashboard_' . auth()->id();
        
        // Cache for 2 minutes only (reduced from 5 minutes for more real-time data)
        $data = Cache::remember($cacheKey, 120, function() {
            // Get all counts in optimized queries
            $userCounts = User::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN role = 1 THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN role = 2 THEN 1 ELSE 0 END) as registrars,
                SUM(CASE WHEN role = 3 THEN 1 ELSE 0 END) as teachers,
                SUM(CASE WHEN role = 4 THEN 1 ELSE 0 END) as students,
                SUM(CASE WHEN email_verified_at IS NULL THEN 1 ELSE 0 END) as unverified
            ')->first();
            
            // Get pending users directly WITHOUT caching
            $pendingUsers = User::where('is_approved', false)
                ->select(['id', 'f_name', 'l_name', 'email', 'role', 'created_at'])
                ->latest()
                ->take(5)
                ->get();
            
            // Get unverified users
            $unverifiedUsers = User::whereNull('email_verified_at')
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
                'unverifiedUsers' => $unverifiedUsers,
                'unverifiedCount' => $userCounts->unverified ?? 0,
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
    
    /**
     * Clear dashboard cache for current user
     * Useful when data changes and needs immediate refresh
     */
    public function clearCache()
    {
        Cache::forget('admin_dashboard_' . auth()->id());
        Cache::forget('today_logins_' . date('Y-m-d'));
        
        return redirect()->back()->with('success', 'Dashboard cache cleared successfully.');
    }
}