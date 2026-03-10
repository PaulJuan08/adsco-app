<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\College;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    //  INDEX — General analytics overview
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $activeTab  = $request->get('tab', 'overview');
        $collegeId  = $request->get('college_id');
        $programId  = $request->get('program_id');
        $year       = $request->get('year');
        $searchName = $request->get('search_name');

        // ── Overview stats ───────────────────────────────────────────
        $totalStudents    = User::where('role', 4)->where('is_approved', 1)->whereNotNull('email_verified_at')->count();
        $totalTeachers    = User::where('role', 3)->count();
        $totalCourses     = Course::count();
        $totalTopics      = Topic::count();
        $totalQuizzes     = Quiz::count();
        $totalAssignments = Assignment::count();

        // ── Topic completion overview ────────────────────────────────
        $topicCompletionStats = DB::table('progress')
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        // ── Quiz pass / fail ─────────────────────────────────────────
        $quizPassCount = QuizAttempt::whereNotNull('completed_at')->where('passed', true)->count();
        $quizFailCount = QuizAttempt::whereNotNull('completed_at')->where('passed', false)->count();

        // ── Assignment submission status ──────────────────────────────
        $submissionStatus = AssignmentSubmission::selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        // ── Monthly enrollments (last 6 months) ─────────────────────
        $monthlyEnrollments = $this->fillMonths(
            Enrollment::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as cnt")
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('cnt', 'month')
        );

        // ── Monthly quiz attempts (last 6 months) ────────────────────
        $monthlyAttempts = $this->fillMonths(
            QuizAttempt::selectRaw("DATE_FORMAT(completed_at, '%Y-%m') as month, COUNT(*) as cnt")
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('cnt', 'month')
        );

        // ── Monthly submissions (last 6 months) ──────────────────────
        $monthlySubmissions = $this->fillMonths(
            AssignmentSubmission::selectRaw("DATE_FORMAT(submitted_at, '%Y-%m') as month, COUNT(*) as cnt")
                ->whereNotNull('submitted_at')
                ->where('submitted_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('cnt', 'month')
        );

        // ── Top 10 students by avg quiz score ────────────────────────
        $topStudents = DB::table('quiz_attempts')
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->where('users.role', 4)
            ->whereNotNull('quiz_attempts.completed_at')
            ->selectRaw("users.id, CONCAT(users.f_name, ' ', users.l_name) as name, ROUND(AVG(quiz_attempts.percentage),1) as avg_score, COUNT(*) as attempt_count")
            ->groupBy('users.id', 'users.f_name', 'users.l_name')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        // ── Top 10 courses by enrollment ─────────────────────────────
        $topCourses = Course::withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(10)
            ->get(['id', 'title', 'course_code']);

        // ── Students tab ─────────────────────────────────────────────
        $studentsQuery = User::where('role', 4)
            ->where('is_approved', 1)
            ->whereNotNull('email_verified_at')
            ->with(['college', 'program'])
            ->withCount([
                'enrollments as courses_count',
                'quizAttempts as quiz_attempts_count',
                'assignmentSubmissions as submissions_count',
            ]);

        if ($collegeId) $studentsQuery->where('college_id', $collegeId);
        if ($programId) $studentsQuery->where('program_id', $programId);
        if ($year)      $studentsQuery->where('college_year', $year);
        if ($searchName) {
            $studentsQuery->where(function ($q) use ($searchName) {
                $q->where('f_name', 'like', "%{$searchName}%")
                  ->orWhere('l_name', 'like', "%{$searchName}%")
                  ->orWhere('student_id', 'like', "%{$searchName}%");
            });
        }
        $students = $studentsQuery->orderBy('l_name')->paginate(20)->withQueryString();

        // ── Teachers tab ─────────────────────────────────────────────
        $teachersQuery = User::where('role', 3)
            ->select('id', 'f_name', 'l_name', 'employee_id', 'college_id', 'email')
            ->selectRaw("(SELECT COUNT(*) FROM courses WHERE teacher_id = users.id) as courses_count")
            ->selectRaw("(SELECT COUNT(*) FROM topics WHERE created_by = users.id) as topics_count")
            ->selectRaw("(SELECT COUNT(*) FROM quizzes WHERE created_by = users.id) as quizzes_count")
            ->selectRaw("(SELECT COUNT(*) FROM assignments WHERE created_by = users.id) as assignments_count")
            ->with('college');

        if ($searchName) {
            $teachersQuery->where(function ($q) use ($searchName) {
                $q->where('f_name', 'like', "%{$searchName}%")
                  ->orWhere('l_name', 'like', "%{$searchName}%")
                  ->orWhere('employee_id', 'like', "%{$searchName}%");
            });
        }
        $teachers = $teachersQuery->orderBy('l_name')->paginate(20)->withQueryString();

        // ── Filters ──────────────────────────────────────────────────
        $colleges = College::where('status', 1)->orderBy('college_name')->get();
        $programs = $collegeId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years = User::where('role', 4)->whereNotNull('college_year')
            ->distinct()->pluck('college_year')->sort()->values();

        return view('admin.analytics.index', compact(
            'activeTab',
            'totalStudents', 'totalTeachers', 'totalCourses',
            'totalTopics', 'totalQuizzes', 'totalAssignments',
            'topicCompletionStats', 'quizPassCount', 'quizFailCount',
            'submissionStatus', 'monthlyEnrollments', 'monthlyAttempts', 'monthlySubmissions',
            'topStudents', 'topCourses',
            'students', 'teachers',
            'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    //  STUDENT — Individual student analytics
    // ══════════════════════════════════════════════════════════════════

    public function student(Request $request, string $encryptedId)
    {
        $id      = Crypt::decrypt($encryptedId);
        $student = User::where('role', 4)->with(['college', 'program'])->findOrFail($id);

        // Enrolled courses with per-topic progress
        $enrolledCourses = Course::whereHas('enrollments', fn($q) => $q->where('student_id', $id))
            ->with('topics')
            ->get()
            ->map(function ($course) use ($id) {
                $topicIds    = $course->topics->pluck('id');
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

        // Quiz attempts (oldest first for trend)
        $quizAttempts = QuizAttempt::where('user_id', $id)
            ->with('quiz.course')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at')
            ->get();

        $quizPassCount = $quizAttempts->where('passed', true)->count();
        $quizFailCount = $quizAttempts->where('passed', false)->count();

        // Quiz trend (last 15 chronological)
        $quizTrend = $quizAttempts->reverse()->take(15)->reverse()->values()->map(fn($a) => [
            'date'  => $a->completed_at->format('M d'),
            'score' => round($a->percentage, 1),
            'quiz'  => \Str::limit($a->quiz->title ?? 'Quiz', 25),
        ]);

        // Avg score per course (horizontal bar)
        $avgScorePerCourse = $quizAttempts
            ->groupBy(fn($a) => \Str::limit($a->quiz->course->title ?? 'N/A', 22))
            ->map(fn($g) => round($g->avg('percentage'), 1));

        // Course topic progress (bar chart)
        $courseTopicProgress = $enrolledCourses->map(fn($ec) => [
            'course' => \Str::limit($ec['course']->title, 22),
            'pct'    => $ec['pct'],
        ])->values();

        // Assignment submissions
        $submissions = AssignmentSubmission::where('student_id', $id)
            ->with('assignment.course')
            ->latest('submitted_at')
            ->get();

        $submissionStatus = $submissions->groupBy('status')->map->count();

        $gradedSubs = $submissions->where('status', 'graded')->whereNotNull('score');
        $avgAssignmentScore = $gradedSubs->count() > 0
            ? round($gradedSubs->avg('score'), 1)
            : null;

        // Quiz score per quiz title (bar chart — last 10 distinct quizzes)
        $quizScoreBar = $quizAttempts->reverse()->take(10)->reverse()->values()->map(fn($a) => [
            'label' => \Str::limit($a->quiz->title ?? 'Quiz', 20),
            'score' => round($a->percentage, 1),
            'passed'=> $a->passed,
        ]);

        return view('admin.analytics.student', compact(
            'student', 'encryptedId',
            'enrolledCourses',
            'quizAttempts', 'quizTrend', 'quizPassCount', 'quizFailCount',
            'avgScorePerCourse', 'courseTopicProgress',
            'submissions', 'submissionStatus', 'avgAssignmentScore',
            'quizScoreBar'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    //  TEACHER — Individual teacher analytics
    // ══════════════════════════════════════════════════════════════════

    public function teacher(Request $request, string $encryptedId)
    {
        $id      = Crypt::decrypt($encryptedId);
        $teacher = User::where('role', 3)->findOrFail($id);

        // Courses taught (primary + additionally assigned)
        $courses = Course::where(function ($q) use ($id) {
            $q->where('teacher_id', $id)
              ->orWhereHas('teachers', fn($tq) => $tq->where('users.id', $id));
        })->withCount(['enrollments', 'topics', 'quizzes', 'assignments'])
          ->get();

        $courseIds = $courses->pluck('id');
        $totalStudents = Enrollment::whereIn('course_id', $courseIds)->distinct('student_id')->count('student_id');

        $topicsCreated      = Topic::where('created_by', $id)->count();
        $quizzesCreated     = Quiz::where('created_by', $id)->count();
        $assignmentsCreated = Assignment::where('created_by', $id)->count();

        // Students per course (bar)
        $enrollmentsPerCourse = $courses->map(fn($c) => [
            'course'   => \Str::limit($c->title, 22),
            'students' => $c->enrollments_count,
        ]);

        // Avg quiz score per course (horizontal bar)
        $avgScorePerCourse = $courses->map(function ($c) {
            $avg = QuizAttempt::whereHas('quiz', fn($q) => $q->where('course_id', $c->id))
                ->whereNotNull('completed_at')
                ->avg('percentage') ?? 0;
            return ['course' => \Str::limit($c->title, 22), 'avg' => round($avg, 1)];
        });

        // Assignment submission stats (doughnut)
        $quizIds       = Quiz::whereIn('course_id', $courseIds)->pluck('id');
        $assignmentIds = Assignment::whereIn('course_id', $courseIds)->pluck('id');

        $submissionStats = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        // Quiz pass/fail (doughnut)
        $quizPassCount = QuizAttempt::whereIn('quiz_id', $quizIds)->where('passed', true)->count();
        $quizFailCount = QuizAttempt::whereIn('quiz_id', $quizIds)->where('passed', false)
            ->whereNotNull('completed_at')->count();

        // Monthly quiz attempts trend (line)
        $monthlyAttempts = $this->fillMonths(
            QuizAttempt::whereIn('quiz_id', $quizIds)
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', now()->subMonths(5)->startOfMonth())
                ->selectRaw("DATE_FORMAT(completed_at, '%Y-%m') as month, COUNT(*) as cnt")
                ->groupBy('month')->orderBy('month')
                ->pluck('cnt', 'month')
        );

        // Content over time (line — quizzes + assignments created)
        $quizCreatedMonthly = $this->fillMonths(
            Quiz::where('created_by', $id)
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as cnt")
                ->groupBy('month')->orderBy('month')
                ->pluck('cnt', 'month')
        );

        // Recent students
        $studentIds = Enrollment::whereIn('course_id', $courseIds)->distinct()->pluck('student_id');
        $recentStudents = User::whereIn('id', $studentIds)
            ->where('role', 4)
            ->with(['college', 'program'])
            ->select('id', 'f_name', 'l_name', 'student_id', 'college_id', 'program_id', 'college_year')
            ->withCount([
                'quizAttempts as quiz_attempts_count',
                'assignmentSubmissions as submissions_count',
            ])
            ->orderBy('l_name')
            ->paginate(15)->withQueryString();

        return view('admin.analytics.teacher', compact(
            'teacher', 'encryptedId',
            'courses', 'totalStudents',
            'topicsCreated', 'quizzesCreated', 'assignmentsCreated',
            'enrollmentsPerCourse', 'avgScorePerCourse',
            'submissionStats', 'quizPassCount', 'quizFailCount',
            'monthlyAttempts', 'quizCreatedMonthly',
            'recentStudents'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    //  HELPER — Fill missing months with 0
    // ══════════════════════════════════════════════════════════════════

    private function fillMonths($data)
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $months[$key] = $data[$key] ?? 0;
        }
        return $months;
    }
}
