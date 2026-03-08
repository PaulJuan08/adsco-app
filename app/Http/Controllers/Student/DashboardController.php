<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\Assignment;
use App\Models\Topic;
use App\Models\QuizAttempt;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            
            // Get available quizzes (published, not past due_date)
            $availableQuizzes = Quiz::where('is_published', true)
                ->where(function($query) {
                    $query->whereNull('due_date')
                        ->orWhere('due_date', '>', now());
                })
                ->latest()
                ->limit(5)
                ->get(['id', 'title', 'description', 'due_date']);

            // Get upcoming quizzes (due within next 7 days)
            $upcomingQuizzes = Quiz::where('is_published', true)
                ->where('due_date', '>', now())
                ->where('due_date', '<=', now()->addDays(7))
                ->orderBy('due_date')
                ->limit(3)
                ->get(['id', 'title', 'due_date']);
            
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
        
        // Chart data — not cached so scores are always fresh
        $quizChartData = QuizAttempt::where('user_id', $studentId)
            ->with('quiz:id,title')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->take(7)
            ->get()
            ->reverse()
            ->map(fn($a) => [
                'label'      => Str::limit($a->quiz->title ?? 'Quiz', 20),
                'percentage' => round($a->percentage ?? 0, 1),
                'passed'     => (bool) $a->passed,
            ])
            ->values();

        $assignmentChartData = AssignmentSubmission::where('student_id', $studentId)
            ->where('status', 'graded')
            ->with('assignment:id,title,points')
            ->latest('graded_at')
            ->take(7)
            ->get()
            ->reverse()
            ->map(function ($s) {
                $pts = $s->assignment->points ?? 1;
                $pct = $pts > 0 ? round(($s->score / $pts) * 100, 1) : 0;
                return [
                    'label'      => Str::limit($s->assignment->title ?? 'Assignment', 20),
                    'percentage' => $pct,
                    'score'      => (int) ($s->score ?? 0),
                    'total'      => (int) $pts,
                ];
            })
            ->values();

        return view('student.dashboard', array_merge($data, [
            'quizChartData'       => $quizChartData,
            'assignmentChartData' => $assignmentChartData,
        ]));
    }
    
    public function clearCache()
    {
        Cache::forget('student_dashboard_' . auth()->id());
        return redirect()->back()->with('success', 'Dashboard cache cleared successfully.');
    }
}