<?php
// app/Http/Controllers/Student/DashboardController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\Assignment;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();
        $cacheKey = 'student_dashboard_' . $studentId;
        
        $data = Cache::remember($cacheKey, 180, function() use ($studentId) {
            // Get enrolled courses IDs first
            $enrolledCourseIds = Enrollment::where('student_id', $studentId)
                ->where('status', 'active')
                ->pluck('course_id')
                ->toArray();
            
            // Get enrolled courses with topics count
            $enrolledCourses = Enrollment::where('student_id', $studentId)
                ->where('status', 'active')
                ->with([
                    'course.teacher:id,f_name,l_name',
                    'course' => function($query) {
                        $query->withCount('topics');
                    }
                ])
                ->get();
            
            // Get all completed topic IDs for this student
            $completedTopicIds = DB::table('progress')
                ->where('student_id', $studentId)
                ->where('status', 'completed')
                ->pluck('topic_id')
                ->toArray();
            
            // Get completed topics per course using course_topics pivot table
            $completedTopicsMap = [];
            if (!empty($completedTopicIds) && !empty($enrolledCourseIds)) {
                $topicCourses = DB::table('course_topics')
                    ->whereIn('topic_id', $completedTopicIds)
                    ->whereIn('course_id', $enrolledCourseIds)
                    ->select('course_id', 'topic_id')
                    ->get();
                
                foreach ($topicCourses as $tc) {
                    if (!isset($completedTopicsMap[$tc->course_id])) {
                        $completedTopicsMap[$tc->course_id] = 0;
                    }
                    $completedTopicsMap[$tc->course_id]++;
                }
            }
            
            // Calculate progress for each course using ACTUAL data
            $totalProgress = 0;
            $completedCourses = 0;
            $totalTopicsCount = 0;
            $completedTopicsCount = 0;
            
            foreach ($enrolledCourses as $enrollment) {
                if ($enrollment->course) {
                    $courseId = $enrollment->course->id;
                    $courseTotalTopics = $enrollment->course->topics_count ?? 0;
                    $courseCompletedTopics = $completedTopicsMap[$courseId] ?? 0;
                    
                    // Calculate progress percentage
                    $progress = $courseTotalTopics > 0 
                        ? round(($courseCompletedTopics / $courseTotalTopics) * 100, 1) 
                        : 0;
                    
                    // Store progress on enrollment and course objects
                    $enrollment->progress = $progress;
                    $enrollment->course->progress = $progress;
                    $enrollment->course->completed_topics = $courseCompletedTopics;
                    $enrollment->course->total_topics = $courseTotalTopics;
                    
                    // Add to totals
                    $totalProgress += $progress;
                    $totalTopicsCount += $courseTotalTopics;
                    $completedTopicsCount += $courseCompletedTopics;
                    
                    // Check if course is completed (progress 100%)
                    if ($progress >= 100) {
                        $completedCourses++;
                    }
                }
            }
            
            $averageProgress = count($enrolledCourses) > 0 
                ? round($totalProgress / count($enrolledCourses), 1) 
                : 0;
            
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
            
            // Get recent topics from enrolled courses
            $recentTopics = Topic::whereHas('courses', function($query) use ($enrolledCourseIds) {
                    $query->whereIn('courses.id', $enrolledCourseIds);
                })
                ->where('is_published', 1)
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
                    'total_courses' => count($enrolledCourses),
                    'completed_courses' => $completedCourses,
                    'in_progress_courses' => count($enrolledCourses) - $completedCourses,
                    'total_topics' => $totalTopicsCount,
                    'completed_topics' => $completedTopicsCount,
                    'average_progress' => $averageProgress,
                ],
                
                // Courses
                'enrolledCourses' => $enrolledCourses,
                'completedCourses' => $completedCourses,
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
                'totalTopics' => $totalTopicsCount,
                'totalQuizzes' => Quiz::where('is_published', 1)->count(),
            ];
        });
        
        return view('student.dashboard', $data);
    }
    
    public function clearCache()
    {
        Cache::forget('student_dashboard_' . auth()->id());
        return redirect()->back()->with('success', 'Dashboard cache cleared successfully.');
    }
}