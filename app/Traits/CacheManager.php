<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use App\Models\Enrollment;

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
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('teacher_topics_index_' . $teacherId . '_page_' . $page);
        }
        
        // Clear teacher topic stats
        Cache::forget('teacher_topics_stats_' . $teacherId);
        Cache::forget('teacher_dashboard_' . $teacherId);
    }
    
    /**
     * Clear admin course caches
     */
    protected function clearAdminCourseCaches()
    {
        // Clear admin course index pages
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('courses_index_page_' . $page);
            Cache::forget('admin_courses_index_page_' . $page);
        }
        
        // Clear admin stats caches
        Cache::forget('draft_courses_count');
        Cache::forget('avg_students_per_course');
        Cache::forget('total_students_count');
        Cache::forget('admin_dashboard_' . auth()->id());
    }
    
    /**
     * Clear admin topic caches
     */
    protected function clearAdminTopicCaches()
    {
        // Clear admin topic index pages
        for ($page = 1; $page <= 5; $page++) {
            Cache::forget('admin_topics_index_page_' . $page);
        }
        
        // Clear admin topic stats
        Cache::forget('admin_topics_stats');
        Cache::forget('admin_dashboard_' . auth()->id());
    }
}