<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $studentId = Auth::id();

        // Enrolled courses with per-topic progress
        $enrolledCourses = Course::whereHas('enrollments', fn($q) => $q->where('student_id', $studentId))
            ->with('topics')
            ->get()
            ->map(function ($course) use ($studentId) {
                $topicIds = $course->topics->pluck('id');
                $progressRows = DB::table('progress')
                    ->where('student_id', $studentId)
                    ->whereIn('topic_id', $topicIds)
                    ->pluck('status', 'topic_id');
                $completed  = $progressRows->filter(fn($s) => $s === 'completed')->count();
                $inProgress = $progressRows->filter(fn($s) => $s === 'in_progress')->count();
                $total      = $course->topics->count();
                return [
                    'course'      => $course,
                    'total'       => $total,
                    'completed'   => $completed,
                    'in_progress' => $inProgress,
                    'not_started' => max(0, $total - $completed - $inProgress),
                    'pct'         => $total > 0 ? round(($completed / $total) * 100) : 0,
                ];
            });

        // Overall topic summary
        $totalTopics     = $enrolledCourses->sum('total');
        $completedCount  = $enrolledCourses->sum('completed');
        $inProgressCount = $enrolledCourses->sum('in_progress');
        $notStartedCount = max(0, $totalTopics - $completedCount - $inProgressCount);

        // Quiz attempts
        $quizAttempts = QuizAttempt::where('user_id', $studentId)
            ->with('quiz.course')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->get();

        $quizPassCount = $quizAttempts->where('passed', true)->count();
        $quizFailCount = $quizAttempts->where('passed', false)->count();
        $avgScore      = $quizAttempts->count() > 0
            ? round($quizAttempts->avg('percentage'), 1)
            : null;

        // Quiz score trend (last 10 attempts, oldest→newest)
        $quizTrend = $quizAttempts->reverse()->take(10)->values()->map(fn($a) => [
            'date'  => $a->completed_at->format('M d'),
            'score' => round($a->percentage, 1),
            'quiz'  => \Str::limit($a->quiz->title ?? 'Quiz', 25),
        ]);

        // Assignment submissions
        $submissions = AssignmentSubmission::where('student_id', $studentId)
            ->with('assignment.course')
            ->latest('submitted_at')
            ->get();

        $submissionStats = [
            'submitted' => $submissions->where('status', 'submitted')->count(),
            'graded'    => $submissions->where('status', 'graded')->count(),
            'late'      => $submissions->where('status', 'late')->count(),
        ];

        $gradedSubs = $submissions->where('status', 'graded')->whereNotNull('score');
        $avgAssignmentScore = $gradedSubs->count() > 0
            ? round($gradedSubs->avg('score'), 1)
            : null;

        // Per-course topic progress for bar chart
        $courseTopicProgress = $enrolledCourses->map(fn($ec) => [
            'course' => \Str::limit($ec['course']->title, 22),
            'pct'    => $ec['pct'],
        ])->values();

        return view('student.analytics.index', compact(
            'enrolledCourses',
            'totalTopics', 'completedCount', 'inProgressCount', 'notStartedCount',
            'quizAttempts', 'quizPassCount', 'quizFailCount', 'avgScore', 'quizTrend',
            'submissions', 'submissionStats', 'avgAssignmentScore',
            'courseTopicProgress'
        ));
    }

    public function grades(Request $request)
    {
        return redirect()->route('student.progress.index');
    }
}
