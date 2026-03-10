<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\College;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // ── Helper: teacher's accessible course IDs ──────────────────────
    private function teacherCourseIds(int $teacherId)
    {
        return Course::where(function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)
              ->orWhereHas('teachers', fn($tq) => $tq->where('users.id', $teacherId));
        })->pluck('id');
    }

    // ── Helper: fill 6 months with 0s ────────────────────────────────
    private function fillMonths($data)
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $months[$key] = $data[$key] ?? 0;
        }
        return $months;
    }

    // ══════════════════════════════════════════════════════════════════
    //  INDEX — Teacher's overview analytics
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $teacherId  = Auth::id();
        $activeTab  = $request->get('tab', 'overview');
        $collegeId  = $request->get('college_id');
        $year       = $request->get('year');
        $searchName = $request->get('search_name');

        $courseIds = $this->teacherCourseIds($teacherId);

        $myCourses = Course::whereIn('id', $courseIds)
            ->withCount(['enrollments as students_count', 'topics', 'quizzes', 'assignments'])
            ->get();

        $studentIds = Enrollment::whereIn('course_id', $courseIds)->distinct()->pluck('student_id');
        $totalStudents = $studentIds->count();

        $quizIds       = Quiz::whereIn('course_id', $courseIds)->pluck('id');
        $assignmentIds = Assignment::whereIn('course_id', $courseIds)->pluck('id');

        // ── Overview stats ───────────────────────────────────────────
        $totalAttempts      = QuizAttempt::whereIn('quiz_id', $quizIds)->whereNotNull('completed_at')->count();
        $passCount          = QuizAttempt::whereIn('quiz_id', $quizIds)->where('passed', true)->count();
        $failCount          = QuizAttempt::whereIn('quiz_id', $quizIds)->where('passed', false)->whereNotNull('completed_at')->count();
        $avgScore           = QuizAttempt::whereIn('quiz_id', $quizIds)->whereNotNull('percentage')->avg('percentage');
        $totalSubmissions   = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)->count();
        $pendingSubmissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)->whereIn('status', ['submitted', 'late'])->count();
        $gradedSubmissions  = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)->where('status', 'graded')->count();

        // ── Charts data ──────────────────────────────────────────────
        $enrollmentsPerCourse = $myCourses->map(fn($c) => [
            'course'   => \Str::limit($c->title, 22),
            'students' => $c->students_count,
        ]);

        $avgScorePerCourse = $myCourses->map(function ($c) {
            $avg = QuizAttempt::whereHas('quiz', fn($q) => $q->where('course_id', $c->id))
                ->whereNotNull('completed_at')
                ->avg('percentage') ?? 0;
            return ['course' => \Str::limit($c->title, 22), 'avg' => round($avg, 1)];
        });

        $submissionStats = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $monthlyAttempts = $this->fillMonths(
            QuizAttempt::whereIn('quiz_id', $quizIds)
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', now()->subMonths(5)->startOfMonth())
                ->selectRaw("DATE_FORMAT(completed_at, '%Y-%m') as month, COUNT(*) as cnt")
                ->groupBy('month')->orderBy('month')
                ->pluck('cnt', 'month')
        );

        $monthlySubmissions = $this->fillMonths(
            AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
                ->whereNotNull('submitted_at')
                ->where('submitted_at', '>=', now()->subMonths(5)->startOfMonth())
                ->selectRaw("DATE_FORMAT(submitted_at, '%Y-%m') as month, COUNT(*) as cnt")
                ->groupBy('month')->orderBy('month')
                ->pluck('cnt', 'month')
        );

        // Top students in this teacher's courses (by avg quiz score)
        $topStudents = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->whereIn('quiz_attempts.quiz_id', $quizIds)
            ->whereNotNull('quiz_attempts.completed_at')
            ->selectRaw("users.id, CONCAT(users.f_name, ' ', users.l_name) as name, ROUND(AVG(quiz_attempts.percentage),1) as avg_score, COUNT(*) as attempt_count")
            ->groupBy('users.id', 'users.f_name', 'users.l_name')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        // ── Students tab ─────────────────────────────────────────────
        $studentsQuery = User::where('role', 4)
            ->whereIn('id', $studentIds)
            ->with(['college', 'program'])
            ->withCount([
                'quizAttempts as quiz_attempts_count',
                'assignmentSubmissions as submissions_count',
            ]);

        if ($collegeId) $studentsQuery->where('college_id', $collegeId);
        if ($year)      $studentsQuery->where('college_year', $year);
        if ($searchName) {
            $studentsQuery->where(function ($q) use ($searchName) {
                $q->where('f_name', 'like', "%{$searchName}%")
                  ->orWhere('l_name', 'like', "%{$searchName}%")
                  ->orWhere('student_id', 'like', "%{$searchName}%");
            });
        }
        $students = $studentsQuery->orderBy('l_name')->paginate(20)->withQueryString();

        // Filters
        $colleges = College::where('status', 1)->orderBy('college_name')->get();
        $years    = User::whereIn('id', $studentIds)->whereNotNull('college_year')
            ->distinct()->pluck('college_year')->sort()->values();

        return view('teacher.analytics.index', compact(
            'activeTab',
            'myCourses', 'totalStudents',
            'totalAttempts', 'passCount', 'failCount', 'avgScore',
            'totalSubmissions', 'pendingSubmissions', 'gradedSubmissions',
            'enrollmentsPerCourse', 'avgScorePerCourse',
            'submissionStats', 'monthlyAttempts', 'monthlySubmissions',
            'topStudents', 'students',
            'colleges', 'years',
            'collegeId', 'year', 'searchName'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    //  STUDENT — Individual student (in teacher's courses only)
    // ══════════════════════════════════════════════════════════════════

    public function student(Request $request, string $encryptedId)
    {
        $teacherId = Auth::id();
        $id        = Crypt::decrypt($encryptedId);

        // Security: student must be enrolled in one of this teacher's courses
        $courseIds = $this->teacherCourseIds($teacherId);
        abort_if(
            !Enrollment::where('student_id', $id)->whereIn('course_id', $courseIds)->exists(),
            403, 'This student is not enrolled in your courses.'
        );

        $student = User::where('role', 4)->with(['college', 'program'])->findOrFail($id);

        $quizIds       = Quiz::whereIn('course_id', $courseIds)->pluck('id');
        $assignmentIds = Assignment::whereIn('course_id', $courseIds)->pluck('id');

        // Enrolled courses with per-topic progress (teacher's courses only)
        $enrolledCourses = Course::whereIn('id', $courseIds)
            ->whereHas('enrollments', fn($q) => $q->where('student_id', $id))
            ->with('topics')
            ->get()
            ->map(function ($course) use ($id) {
                $topicIds     = $course->topics->pluck('id');
                $progressRows = DB::table('progress')
                    ->where('student_id', $id)
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

        // Quiz attempts (teacher's quizzes only)
        $quizAttempts = QuizAttempt::where('user_id', $id)
            ->whereIn('quiz_id', $quizIds)
            ->with('quiz.course')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at')
            ->get();

        $quizPassCount = $quizAttempts->where('passed', true)->count();
        $quizFailCount = $quizAttempts->where('passed', false)->count();

        $quizTrend = $quizAttempts->reverse()->take(15)->reverse()->values()->map(fn($a) => [
            'date'  => $a->completed_at->format('M d'),
            'score' => round($a->percentage, 1),
            'quiz'  => \Str::limit($a->quiz->title ?? 'Quiz', 25),
        ]);

        $avgScorePerCourse = $quizAttempts
            ->groupBy(fn($a) => \Str::limit($a->quiz->course->title ?? 'N/A', 22))
            ->map(fn($g) => round($g->avg('percentage'), 1));

        $courseTopicProgress = $enrolledCourses->map(fn($ec) => [
            'course' => \Str::limit($ec['course']->title, 22),
            'pct'    => $ec['pct'],
        ])->values();

        // Assignment submissions (teacher's assignments only)
        $submissions = AssignmentSubmission::where('student_id', $id)
            ->whereIn('assignment_id', $assignmentIds)
            ->with('assignment.course')
            ->latest('submitted_at')
            ->get();

        $submissionStatus = $submissions->groupBy('status')->map->count();
        $gradedSubs = $submissions->where('status', 'graded')->whereNotNull('score');
        $avgAssignmentScore = $gradedSubs->count() > 0
            ? round($gradedSubs->avg('score'), 1)
            : null;

        return view('teacher.analytics.student', compact(
            'student', 'encryptedId',
            'enrolledCourses',
            'quizAttempts', 'quizTrend', 'quizPassCount', 'quizFailCount',
            'avgScorePerCourse', 'courseTopicProgress',
            'submissions', 'submissionStatus', 'avgAssignmentScore'
        ));
    }
}
