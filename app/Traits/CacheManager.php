<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use App\Models\Enrollment;
use App\Models\User;

trait CacheManager
{
    /**
     * Clear all student caches for a specific course
     */
    protected function clearStudentCachesForCourse($courseId)
    {
        // Get all enrolled students for this course
        $enrolledStudents = Enrollment::where('course_id', $courseId)
            ->where('status', 'active')
            ->pluck('student_id')
            ->toArray();
        
        foreach ($enrolledStudents as $studentId) {
            // Clear student course index pages (pages 1-5)
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
            }
            
            // Clear course show cache
            Cache::forget('student_course_show_' . $courseId);
            
            // Clear course progress cache
            Cache::forget('student_course_progress_' . $studentId . '_' . $courseId);
            
            // Clear student overall stats
            Cache::forget('student_overall_stats_' . $studentId);
            
            // Clear recent activities
            Cache::forget('student_recent_activities_' . $studentId);
            
            // Clear dashboard
            Cache::forget('student_dashboard_' . $studentId);
            
            // Clear topic index pages
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_topics_index_' . $studentId . '_page_' . $page);
            }
            
            // Clear total topics count
            Cache::forget('student_total_topics_' . $studentId);
            
            // Clear specific topic show caches for this course
            $course = \App\Models\Course::find($courseId);
            if ($course) {
                foreach ($course->topics as $topic) {
                    Cache::forget('student_topic_show_' . $topic->id . '_student_' . $studentId);
                }
            }
        }
    }
    
    /**
     * Clear all student caches for all courses
     */
    protected function clearAllStudentCaches()
    {
        // Get all active students
        $activeStudents = Enrollment::where('status', 'active')
            ->distinct('student_id')
            ->pluck('student_id')
            ->toArray();
        
        foreach ($activeStudents as $studentId) {
            // Clear course index pages (pages 1-5)
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_courses_index_' . $studentId . '_page_' . $page);
            }
            
            // Clear student overall stats
            Cache::forget('student_overall_stats_' . $studentId);
            
            // Clear recent activities
            Cache::forget('student_recent_activities_' . $studentId);
            
            // Clear dashboard
            Cache::forget('student_dashboard_' . $studentId);
            
            // Clear topic index pages
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_topics_index_' . $studentId . '_page_' . $page);
            }
            
            // Clear total topics count
            Cache::forget('student_total_topics_' . $studentId);
        }
    }
    
    /**
     * Clear teacher course caches
     */
    protected function clearTeacherCourseCaches($teacherId)
    {
        // Clear teacher course index pages
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('teacher_courses_index_' . $teacherId . '_page_' . $page);
        }
        
        // Clear teacher stats
        Cache::forget('teacher_courses_stats_' . $teacherId);
        Cache::forget('teacher_dashboard_' . $teacherId);
        
        // Clear teacher enrollments
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('teacher_enrollments_' . $teacherId . '_page_' . $page);
        }
    }
    
    /**
     * Clear teacher topic caches
     */
    protected function clearTeacherTopicCaches($teacherId)
    {
        // Clear teacher topic index pages
        for ($page = 1; $page <= 20; $page++) {
            Cache::forget('teacher_topics_index_' . $teacherId . '_page_' . $page);
        }
        
        // If using Redis, clear pattern-based keys
        if (config('cache.default') === 'redis' || config('cache.default') === 'predis') {
            try {
                $keys = Cache::getRedis()->keys('*teacher_topics_index_' . $teacherId . '_page_*');
                foreach ($keys as $key) {
                    Cache::forget(str_replace(config('cache.prefix'), '', $key));
                }
            } catch (\Exception $e) {
                \Log::warning('Redis key pattern clearing failed', ['error' => $e->getMessage()]);
            }
        }
        
        // Clear teacher topic stats
        Cache::forget('teacher_topics_stats_' . $teacherId);
        Cache::forget('teacher_dashboard_' . $teacherId);
        
        \Log::info('Teacher topic caches cleared for teacher: ' . $teacherId);
    }

    /**
     * Clear ALL teacher topic caches (for all teachers)
     */
    protected function clearAllTeacherTopicCaches()
    {
        // Get all teacher users
        $teachers = \App\Models\User::where('role', 3)->pluck('id')->toArray();
        
        foreach ($teachers as $teacherId) {
            $this->clearTeacherTopicCaches($teacherId);
        }
        
        \Log::info('All teacher topic caches cleared');
    }
    
    /**
     * Clear admin course caches
     */
    protected function clearAdminCourseCaches()
    {
        // Clear admin course index pages - using pattern matching
        if (config('cache.default') === 'redis' || config('cache.default') === 'predis') {
            // For Redis
            try {
                $keys = Cache::getRedis()->keys('*admin_courses_index_page_*');
                foreach ($keys as $key) {
                    Cache::forget(str_replace(config('cache.prefix'), '', $key));
                }
            } catch (\Exception $e) {
                \Log::warning('Redis key pattern clearing failed', ['error' => $e->getMessage()]);
            }
        }
        
        // Clear all possible page numbers (up to 20)
        for ($page = 1; $page <= 20; $page++) {
            Cache::forget('admin_courses_index_page_' . $page);
        }
        
        // Clear admin dashboard
        Cache::forget('admin_dashboard_' . auth()->id());
        
        // Clear all stats caches
        Cache::forget('avg_students_per_course');
        Cache::forget('total_students_count');
        Cache::forget('draft_courses_count');
        
        // Clear teachers cache
        Cache::forget('all_teachers');
        
        \Log::info('Admin course caches cleared');
    }
    
    /**
     * Clear admin topic caches
     */
    protected function clearAdminTopicCaches()
    {
        // Clear all admin topic index pages
        for ($page = 1; $page <= 20; $page++) {
            Cache::forget('admin_topics_index_page_' . $page);
        }
        
        // If using Redis, clear pattern-based keys
        if (config('cache.default') === 'redis' || config('cache.default') === 'predis') {
            try {
                $keys = Cache::getRedis()->keys('*admin_topics_index_page_*');
                foreach ($keys as $key) {
                    Cache::forget(str_replace(config('cache.prefix'), '', $key));
                }
            } catch (\Exception $e) {
                \Log::warning('Redis key pattern clearing failed', ['error' => $e->getMessage()]);
            }
        }
        
        // Clear admin topic stats
        Cache::forget('admin_topics_stats');
        Cache::forget('admin_dashboard_' . auth()->id());
        
        \Log::info('Admin topic caches cleared');
    }
    
    // ============ NEW REGISTRAR CACHE METHODS ============
    
    /**
     * Clear all registrar dashboard caches
     */
    protected function clearRegistrarDashboardCaches()
    {
        // Get all registrar users
        $registrars = User::where('role', 2)->pluck('id')->toArray();
        
        // Clear each registrar's dashboard cache
        foreach ($registrars as $registrarId) {
            Cache::forget('registrar_dashboard_' . $registrarId);
        }
        
        \Log::info('All registrar dashboard caches cleared');
    }
    
    /**
     * Clear registrar user index caches
     */
    protected function clearRegistrarUserIndexCaches($role = null, $status = null)
    {
        // Clear common cache patterns for registrars (pages 1-10)
        for ($page = 1; $page <= 10; $page++) {
            // Clear all users list
            Cache::forget('registrar_users_index_' . md5(json_encode([
                'search' => null,
                'role' => null,
                'status' => null,
                'page' => $page
            ])));
            
            // Clear pending users list
            Cache::forget('registrar_users_index_' . md5(json_encode([
                'search' => null,
                'role' => null,
                'status' => 'pending',
                'page' => $page
            ])));
            
            // Clear teachers list
            Cache::forget('registrar_users_index_' . md5(json_encode([
                'search' => null,
                'role' => 3,
                'status' => null,
                'page' => $page
            ])));
            
            // Clear pending teachers list
            Cache::forget('registrar_users_index_' . md5(json_encode([
                'search' => null,
                'role' => 3,
                'status' => 'pending',
                'page' => $page
            ])));
            
            // Clear students list
            Cache::forget('registrar_users_index_' . md5(json_encode([
                'search' => null,
                'role' => 4,
                'status' => null,
                'page' => $page
            ])));
            
            // Clear pending students list
            Cache::forget('registrar_users_index_' . md5(json_encode([
                'search' => null,
                'role' => 4,
                'status' => 'pending',
                'page' => $page
            ])));
        }
        
        // Clear specific filtered caches if provided
        if ($role || $status) {
            for ($page = 1; $page <= 10; $page++) {
                Cache::forget('registrar_users_index_' . md5(json_encode([
                    'search' => null,
                    'role' => $role,
                    'status' => $status,
                    'page' => $page
                ])));
            }
        }
        
        \Log::info('Registrar user index caches cleared');
    }
    
    /**
     * Clear registrar user specific caches
     */
    protected function clearRegistrarUserCaches($userId = null)
    {
        // Clear registrar user stats caches
        Cache::forget('registrar_user_stats');
        Cache::forget('registrar_pending_users_count');
        Cache::forget('registrar_users_this_month');
        
        // Clear registrar user role options
        Cache::forget('registrar_user_role_options');
        Cache::forget('registrar_user_form_suggestions');
        Cache::forget('registrar_user_role_names');
        
        // Clear specific user caches
        if ($userId) {
            Cache::forget('registrar_user_show_detail_' . $userId);
            Cache::forget('registrar_user_edit_detail_' . $userId);
            Cache::forget('registrar_user_activities_' . $userId);
            Cache::forget('registrar_user_detailed_stats_' . $userId);
            Cache::forget('registrar_user_show_stats_' . auth()->id());
            Cache::forget('registrar_user_edit_stats_' . auth()->id());
        }
        
        \Log::info('Registrar user caches cleared' . ($userId ? ' for user: ' . $userId : ''));
    }
    
    /**
     * Clear all registrar caches (dashboard, index, user)
     */
    protected function clearAllRegistrarCaches($userId = null)
    {
        $this->clearRegistrarDashboardCaches();
        $this->clearRegistrarUserIndexCaches();
        $this->clearRegistrarUserCaches($userId);
        
        \Log::info('All registrar caches cleared');
    }
    
    // ============ NEW ADMIN USER CACHE METHODS ============
    
    /**
     * Clear admin dashboard caches
     */
    protected function clearAdminDashboardCaches()
    {
        // Clear admin dashboard cache for all admins
        $admins = User::where('role', 1)->pluck('id')->toArray();
        foreach ($admins as $adminId) {
            Cache::forget('admin_dashboard_' . $adminId);
        }
        
        \Log::info('Admin dashboard caches cleared');
    }

    
    /**
     * Clear admin user index caches
     */
    protected function clearAdminUserIndexCaches()
    {
        // Clear admin user index pages
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget('users_index_page_' . $page);
            Cache::forget('admin_users_index_page_' . $page);
        }
        
        \Log::info('Admin user index caches cleared');
    }
    
    /**
     * Clear admin user stats caches
     */
    protected function clearAdminUserStatsCaches()
    {
        Cache::forget('user_stats');
        Cache::forget('pending_users_count');
        Cache::forget('users_this_month');
        Cache::forget('admin_dashboard_' . auth()->id());
        
        \Log::info('Admin user stats caches cleared');
    }
    
    /**
     * Clear admin user specific caches
     */
    protected function clearAdminUserCaches($userId = null)
    {
        if ($userId) {
            Cache::forget('user_show_detail_' . $userId);
            Cache::forget('user_edit_detail_' . $userId);
            Cache::forget('user_activities_' . $userId);
            Cache::forget('user_detailed_stats_' . $userId);
            Cache::forget('approver_user_' . $userId);
        }
        
        Cache::forget('user_role_options');
        Cache::forget('user_form_suggestions');
        Cache::forget('user_role_names');
        
        \Log::info('Admin user caches cleared' . ($userId ? ' for user: ' . $userId : ''));
    }
    
    /**
     * Clear all admin caches (dashboard, index, user, stats)
     */
    protected function clearAllAdminCaches($userId = null)
    {
        $this->clearAdminDashboardCaches();
        $this->clearAdminUserIndexCaches();
        $this->clearAdminUserStatsCaches();
        $this->clearAdminUserCaches($userId);
        
        \Log::info('All admin caches cleared');
    }
    
    // ============ COMPREHENSIVE USER REGISTRATION CACHE CLEAR ============
    
    /**
     * Clear all caches when a new user registers
     * This ensures pending users appear immediately in both admin and registrar dashboards
     */
    protected function clearUserRegistrationCaches($user)
    {
        try {
            // Clear admin caches
            $this->clearAllAdminCaches();
            
            // Clear registrar caches
            $this->clearAllRegistrarCaches();
            
            // Clear any other user-related caches
            Cache::forget('user_stats');
            Cache::forget('pending_users_count');
            Cache::forget('users_this_month');
            
            \Log::info('User registration caches cleared for new user: ' . ($user->email ?? 'unknown') . ' (Role: ' . ($user->role ?? 'unknown') . ')');
            
        } catch (\Exception $e) {
            \Log::error('Error clearing user registration caches: ' . $e->getMessage());
        }
    }
    
    // ============ COMPREHENSIVE USER APPROVAL CACHE CLEAR ============
    
    /**
     * Clear all caches when a user is approved
     * This ensures approved users are removed from pending lists
     */
    protected function clearUserApprovalCaches($user)
    {
        try {
            // Clear admin caches
            $this->clearAllAdminCaches($user->id ?? null);
            
            // Clear registrar caches
            $this->clearAllRegistrarCaches($user->id ?? null);
            
            // Clear teacher/student specific caches if applicable
            if ($user && $user->role == 3) { // Teacher
                $this->clearTeacherCourseCaches($user->id);
                $this->clearTeacherTopicCaches($user->id);
                Cache::forget('teacher_dashboard_' . $user->id);
            }
            
            if ($user && $user->role == 4) { // Student
                Cache::forget('student_dashboard_' . $user->id);
                Cache::forget('student_overall_stats_' . $user->id);
                Cache::forget('student_recent_activities_' . $user->id);
                
                for ($page = 1; $page <= 5; $page++) {
                    Cache::forget('student_courses_index_' . $user->id . '_page_' . $page);
                    Cache::forget('student_topics_index_' . $user->id . '_page_' . $page);
                }
            }
            
            \Log::info('User approval caches cleared for user: ' . ($user->email ?? 'unknown'));
            
        } catch (\Exception $e) {
            \Log::error('Error clearing user approval caches: ' . $e->getMessage());
        }
    }

    /**
     * Clear teacher dashboard caches (including topics data)
     */
    protected function clearTeacherDashboardCaches($teacherId = null)
    {
        if ($teacherId) {
            // Clear specific teacher's dashboard
            Cache::forget('teacher_dashboard_' . $teacherId);
        } else {
            // Clear all teacher dashboards
            $teachers = User::where('role', 3)->pluck('id')->toArray();
            foreach ($teachers as $id) {
                Cache::forget('teacher_dashboard_' . $id);
            }
        }
        
        \Log::info('Teacher dashboard caches cleared' . ($teacherId ? ' for teacher: ' . $teacherId : ' for all teachers'));
    }

    /**
     * Clear student quiz caches for all students
     */
    protected function clearAllStudentQuizCaches()
    {
        // Get all student users
        $students = User::where('role', 4)->pluck('id')->toArray();
        
        foreach ($students as $studentId) {
            // Clear quiz index cache
            Cache::forget('student_quizzes_index_' . $studentId);
            
            // Clear quiz stats cache
            Cache::forget('student_quiz_stats_' . $studentId);
            
            // Clear dashboard cache
            Cache::forget('student_dashboard_' . $studentId);
            
            // Clear quiz index pages (up to 5 pages)
            for ($page = 1; $page <= 5; $page++) {
                Cache::forget('student_quizzes_index_' . $studentId . '_page_' . $page);
            }
        }
        
        \Log::info('All student quiz caches cleared for ' . count($students) . ' students');
    }

    /**
     * Clear admin quiz caches
     */
    protected function clearAdminQuizCaches()
    {
        // Clear admin quiz index pages
        for ($page = 1; $page <= 20; $page++) {
            Cache::forget('admin_quizzes_index_page_' . $page);
        }
        
        // Clear admin dashboard cache
        Cache::forget('admin_dashboard_' . auth()->id());
        
        // Clear admin quiz stats
        Cache::forget('admin_quizzes_stats');
        
        \Log::info('Admin quiz caches cleared');
    }

    /**
     * Clear teacher quiz caches
     */
    protected function clearTeacherQuizCaches($teacherId = null)
    {
        if ($teacherId) {
            // Clear specific teacher's quiz caches
            for ($page = 1; $page <= 20; $page++) {
                Cache::forget('teacher_quizzes_index_page_' . $page . '_teacher_' . $teacherId);
            }
            
            // Clear teacher quiz stats
            Cache::forget('teacher_quizzes_stats_' . $teacherId);
            
            // Clear teacher dashboard
            Cache::forget('teacher_dashboard_' . $teacherId);
            
            \Log::info('Teacher quiz caches cleared for teacher: ' . $teacherId);
        } else {
            // Clear all teacher quiz caches
            $teachers = User::where('role', 3)->pluck('id')->toArray();
            foreach ($teachers as $id) {
                $this->clearTeacherQuizCaches($id);
            }
        }
    }

    /**
     * Clear ALL quiz caches across all roles
     */
    protected function clearAllQuizCaches($teacherId = null)
    {
        // Clear admin quiz caches
        $this->clearAdminQuizCaches();
        
        // Clear teacher quiz caches
        if ($teacherId) {
            $this->clearTeacherQuizCaches($teacherId);
        } else {
            $this->clearTeacherQuizCaches();
        }
        
        // Clear student quiz caches
        $this->clearAllStudentQuizCaches();
        
        \Log::info('All quiz caches cleared across all roles');
    }
}