<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentStudentAccess;
use App\Models\AssignmentSubmission;
use App\Models\College;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizStudentAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    //  INDEX — list all quizzes + assignments in a unified To-do view
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $type   = $request->get('type', 'all');   // all | quiz | assignment
        $search = $request->get('search', '');

        $quizzes = collect();
        $assignments = collect();

        if ($type === 'all' || $type === 'quiz') {
            $quizQuery = Quiz::with(['creator'])
                ->withCount(['studentAccess as allowed_students_count' => function ($q) {
                    $q->where('status', 'allowed');
                }])
                ->withCount('attempts');

            if ($search) {
                $quizQuery->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $quizzes = $quizQuery->latest()->get();
        }

        if ($type === 'all' || $type === 'assignment') {
            $assignQuery = Assignment::with(['course', 'creator'])
                ->withCount(['allowedStudents as allowed_students_count'])
                ->withCount('submissions');

            if ($search) {
                $assignQuery->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $assignments = $assignQuery->latest()->get();
        }

        $totalQuizzes      = Quiz::count();
        $totalAssignments  = Assignment::count();
        $totalAccess       = QuizStudentAccess::where('status', 'allowed')->count()
                           + AssignmentStudentAccess::where('status', 'allowed')->count();
        
        // Calculate pending reviews (ungraded submissions)
        $pendingReviews = AssignmentSubmission::whereIn('status', ['submitted', 'late'])->count();

        return view('admin.todo.index', compact(
            'quizzes', 'assignments', 'type', 'search',
            'totalQuizzes', 'totalAssignments', 'totalAccess', 'pendingReviews'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    //  QUIZ ACCESS MANAGEMENT
    // ══════════════════════════════════════════════════════════════════

    public function quizAccess(Request $request, string $encryptedId)
    {
        $id   = Crypt::decrypt($encryptedId);
        $quiz = Quiz::withCount(['studentAccess as allowed_count' => fn($q) => $q->where('status','allowed')])
                    ->with('attempts')
                    ->findOrFail($id);

        // --- filter bar values ---
        $collegeId  = $request->get('college_id');
        $programId  = $request->get('program_id');
        $year       = $request->get('year');
        $searchName = $request->get('search_name');

        $studentQuery = User::where('role', 4)
            ->where('is_approved', 1)
            ->whereNotNull('email_verified_at')
            ->with(['college', 'program']);

        if ($collegeId) {
            $studentQuery->where('college_id', $collegeId);
        }
        
        if ($programId) {
            $studentQuery->where('program_id', $programId);
        }
        
        if ($year) {
            $studentQuery->where('college_year', $year);
        }
        
        if ($searchName) {
            $studentQuery->where(function ($q) use ($searchName) {
                $q->where('f_name', 'like', "%{$searchName}%")
                  ->orWhere('l_name', 'like', "%{$searchName}%")
                  ->orWhere('student_id', 'like', "%{$searchName}%")
                  ->orWhere('email', 'like', "%{$searchName}%");
            });
        }

        $students = $studentQuery->orderBy('l_name')->paginate(20)->withQueryString();

        // Attach access status to each student
        $accessMap = QuizStudentAccess::where('quiz_id', $id)
            ->where('status', 'allowed')
            ->pluck('status', 'student_id');

        foreach ($students as $student) {
            $student->access_status = $accessMap[$student->id] ?? null;
        }

        $colleges = College::where('status', 1)->orderBy('college_name')->get();
        $programs = $collegeId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years    = User::where('role', 4)->whereNotNull('college_year')
                        ->distinct()->pluck('college_year')->sort()->values();

        return view('admin.todo.quiz-access', compact(
            'quiz', 'encryptedId', 'students', 'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName'
        ));
    }

    public function grantQuizAccess(Request $request, string $encryptedId)
    {
        $quizId = Crypt::decrypt($encryptedId);

        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->student_ids as $studentId) {
            QuizStudentAccess::updateOrCreate(
                ['quiz_id' => $quizId, 'student_id' => $studentId],
                [
                    'status'     => 'allowed',
                    'granted_by' => auth()->id(),
                    'granted_at' => now(),
                ]
            );
        }

        Cache::forget('student_todo_' . implode(',', $request->student_ids));

        return back()->with('success', count($request->student_ids) . ' student(s) granted access to quiz.');
    }

    public function revokeQuizAccess(Request $request, string $encryptedId)
    {
        $quizId = Crypt::decrypt($encryptedId);

        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        QuizStudentAccess::where('quiz_id', $quizId)
            ->whereIn('student_id', $request->student_ids)
            ->update(['status' => 'revoked']);

        return back()->with('success', count($request->student_ids) . ' student(s) revoked from quiz.');
    }

    public function toggleQuizAccess(string $encryptedId, int $studentId)
    {
        $quizId = Crypt::decrypt($encryptedId);

        $access = QuizStudentAccess::where('quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->first();

        if (!$access) {
            QuizStudentAccess::create([
                'quiz_id'    => $quizId,
                'student_id' => $studentId,
                'status'     => 'allowed',
                'granted_by' => auth()->id(),
                'granted_at' => now(),
            ]);
            $newStatus = 'allowed';
        } else {
            $newStatus = $access->status === 'allowed' ? 'revoked' : 'allowed';
            $access->update([
                'status'     => $newStatus,
                'granted_by' => auth()->id(),
                'granted_at' => $newStatus === 'allowed' ? now() : $access->granted_at,
            ]);
        }

        return response()->json(['status' => $newStatus, 'message' => 'Access updated.']);
    }

    // ══════════════════════════════════════════════════════════════════
    //  ASSIGNMENT ACCESS MANAGEMENT
    // ══════════════════════════════════════════════════════════════════

    public function assignmentAccess(Request $request, string $encryptedId)
    {
        // Redirect to the unified assignment show page with a flag to open the modal
        return redirect()->route('admin.todo.assignment.show', $encryptedId)
            ->with('open_access_modal', true);
    }

    public function grantAssignmentAccess(Request $request, string $encryptedId)
    {
        $assignmentId = Crypt::decrypt($encryptedId);

        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->student_ids as $studentId) {
            AssignmentStudentAccess::updateOrCreate(
                ['assignment_id' => $assignmentId, 'student_id' => $studentId],
                [
                    'status'     => 'allowed',
                    'granted_by' => auth()->id(),
                    'granted_at' => now(),
                ]
            );
        }

        return back()->with('success', count($request->student_ids) . ' student(s) granted access to assignment.');
    }

    public function revokeAssignmentAccess(Request $request, string $encryptedId)
    {
        $assignmentId = Crypt::decrypt($encryptedId);

        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        AssignmentStudentAccess::where('assignment_id', $assignmentId)
            ->whereIn('student_id', $request->student_ids)
            ->update(['status' => 'revoked']);

        return back()->with('success', count($request->student_ids) . ' student(s) revoked from assignment.');
    }

    public function toggleAssignmentAccess(string $encryptedId, int $studentId)
    {
        $assignmentId = Crypt::decrypt($encryptedId);

        $access = AssignmentStudentAccess::where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();

        if (!$access) {
            AssignmentStudentAccess::create([
                'assignment_id' => $assignmentId,
                'student_id'   => $studentId,
                'status'       => 'allowed',
                'granted_by'   => auth()->id(),
                'granted_at'   => now(),
            ]);
            $newStatus = 'allowed';
        } else {
            $newStatus = $access->status === 'allowed' ? 'revoked' : 'allowed';
            $access->update([
                'status'     => $newStatus,
                'granted_by' => auth()->id(),
                'granted_at' => $newStatus === 'allowed' ? now() : $access->granted_at,
            ]);
        }

        return response()->json(['status' => $newStatus, 'message' => 'Access updated.']);
    }

    // ══════════════════════════════════════════════════════════════════
    //  PROGRESS — admin sees all student quiz + assignment results
    // ══════════════════════════════════════════════════════════════════

    public function progress(Request $request)
    {
        $type       = $request->get('type', 'quiz');
        $collegeId  = $request->get('college_id');
        $programId  = $request->get('program_id');
        $year       = $request->get('year');
        $searchName = $request->get('search_name');
        $itemId     = $request->get('item_id'); // filter by specific quiz or assignment

        $quizProgress       = collect();
        $assignmentProgress = collect();

        if ($type === 'quiz') {
            $query = QuizAttempt::with(['user.college', 'user.program', 'quiz'])
                ->whereHas('user', function ($q) use ($collegeId, $programId, $year, $searchName) {
                    $q->where('role', 4);
                    if ($collegeId) {
                        $q->where('college_id', $collegeId);
                    }
                    if ($programId) {
                        $q->where('program_id', $programId);
                    }
                    if ($year) {
                        $q->where('college_year', $year);
                    }
                    if ($searchName) {
                        $q->where(function ($sq) use ($searchName) {
                            $sq->where('f_name', 'like', "%{$searchName}%")
                               ->orWhere('l_name', 'like', "%{$searchName}%");
                        });
                    }
                });

            if ($itemId) {
                $query->where('quiz_id', $itemId);
            }

            $quizProgress = $query->latest('completed_at')->paginate(25)->withQueryString();
        }

        if ($type === 'assignment') {
            $query = AssignmentSubmission::with(['student.college', 'student.program', 'assignment'])
                ->whereHas('student', function ($q) use ($collegeId, $programId, $year, $searchName) {
                    $q->where('role', 4);
                    if ($collegeId) {
                        $q->where('college_id', $collegeId);
                    }
                    if ($programId) {
                        $q->where('program_id', $programId);
                    }
                    if ($year) {
                        $q->where('college_year', $year);
                    }
                    if ($searchName) {
                        $q->where(function ($sq) use ($searchName) {
                            $sq->where('f_name', 'like', "%{$searchName}%")
                               ->orWhere('l_name', 'like', "%{$searchName}%");
                        });
                    }
                });

            if ($itemId) {
                $query->where('assignment_id', $itemId);
            }

            $assignmentProgress = $query->latest('submitted_at')->paginate(25)->withQueryString();
        }

        $colleges   = College::where('status', 1)->orderBy('college_name')->get();
        $programs   = $collegeId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years      = User::where('role', 4)->whereNotNull('college_year')
                          ->distinct()->pluck('college_year')->sort()->values();
        $quizList   = Quiz::orderBy('title')->get(['id', 'title']);
        $assignList = Assignment::orderBy('title')->get(['id', 'title']);

        return view('admin.todo.progress', compact(
            'type', 'quizProgress', 'assignmentProgress',
            'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName', 'itemId',
            'quizList', 'assignList'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    //  AJAX helpers
    // ══════════════════════════════════════════════════════════════════

    /** Get programs for a college (used by the filter dropdowns) */
    public function getProgramsByCollege(int $collegeId)
    {
        $programs = Program::where('college_id', $collegeId)
            ->orderBy('program_name')
            ->get(['id', 'program_name']);
        return response()->json($programs);
    }

    // ══════════════════════════════════════════════════════════════════
    //  GRADE SUBMISSION
    // ══════════════════════════════════════════════════════════════════

    /**
     * Grade a submission
     */
    public function gradeSubmission(Request $request, int $submissionId)
    {
        try {
            $submission = AssignmentSubmission::with('assignment')->findOrFail($submissionId);

            $request->validate([
                'score'    => 'required|integer|min:0|max:' . ($submission->assignment->points ?? 100),
                'feedback' => 'nullable|string|max:2000',
            ]);

            $submission->update([
                'score'      => $request->score,
                'feedback'   => $request->feedback,
                'status'     => 'graded',
                'graded_by'  => auth()->id(),
                'graded_at'  => now(),
            ]);

            // Redirect back to the assignment show page
            return redirect()->route('admin.todo.assignment.show', Crypt::encrypt($submission->assignment_id))
                ->with('success', 'Submission graded successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error grading submission: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to grade submission: ' . $e->getMessage());
        }
    }

    /**
     * Toggle assignment publish status
     */
    public function toggleAssignmentPublish(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $assignment = Assignment::findOrFail($id);
            
            $assignment->update([
                'is_published' => !$assignment->is_published
            ]);
            
            $status = $assignment->is_published ? 'published' : 'unpublished';
            
            return redirect()->back()->with('success', "Assignment {$status} successfully.");
            
        } catch (\Exception $e) {
            \Log::error('Error toggling assignment publish status: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update assignment status.');
        }
    }

    /**
     * Get assignment access modal content
     */
    public function assignmentAccessModal(Request $request, string $encryptedId)
    {
        $id         = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);

        $collegeId  = $request->get('college_id');
        $programId  = $request->get('program_id');
        $year       = $request->get('year');
        $searchName = $request->get('search_name');

        $studentQuery = User::where('role', 4)
            ->where('is_approved', 1)
            ->whereNotNull('email_verified_at')
            ->with(['college', 'program']);

        if ($collegeId) {
            $studentQuery->where('college_id', $collegeId);
        }
        
        if ($programId) {
            $studentQuery->where('program_id', $programId);
        }
        
        if ($year) {
            $studentQuery->where('college_year', $year);
        }
        
        if ($searchName) {
            $studentQuery->where(function ($q) use ($searchName) {
                $q->where('f_name', 'like', "%{$searchName}%")
                ->orWhere('l_name', 'like', "%{$searchName}%")
                ->orWhere('student_id', 'like', "%{$searchName}%")
                ->orWhere('email', 'like', "%{$searchName}%");
            });
        }

        $students = $studentQuery->orderBy('l_name')->paginate(10);

        $accessMap = AssignmentStudentAccess::where('assignment_id', $id)
            ->where('status', 'allowed')
            ->pluck('status', 'student_id');

        $submissionMap = AssignmentSubmission::where('assignment_id', $id)
            ->pluck('status', 'student_id');

        foreach ($students as $student) {
            $student->access_status     = $accessMap[$student->id] ?? null;
            $student->submission_status = $submissionMap[$student->id] ?? null;
        }

        $colleges = College::where('status', 1)->orderBy('college_name')->get();
        $programs = $collegeId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years    = User::where('role', 4)->whereNotNull('college_year')
                        ->distinct()->pluck('college_year')->sort()->values();

        if ($request->ajax()) {
            return view('admin.todo.partials.assignment-access-modal', compact(
                'assignment', 'encryptedId', 'students', 'colleges', 'programs', 'years',
                'collegeId', 'programId', 'year', 'searchName'
            ))->render();
        }

        return view('admin.todo.partials.assignment-access-modal', compact(
            'assignment', 'encryptedId', 'students', 'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName'
        ));
    }

    /**
     * Show unified assignment details page with access modal
     */
    public function assignmentShow(string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            $assignment = Assignment::with([
                    'course', 
                    'topic', 
                    'submissions.student', 
                    'submissions.gradedBy',
                    'creator'
                ])
                ->withCount([
                    'allowedStudents as allowed_students_count' => function($query) {
                        $query->where('status', 'allowed');
                    },
                    'submissions as submissions_count'
                ])
                ->findOrFail($id);
            
            // Debug - Log the values
            \Log::info('Assignment Show Debug:', [
                'assignment_id' => $assignment->id,
                'allowed_students_count' => $assignment->allowed_students_count,
                'submissions_count' => $assignment->submissions_count,
                'raw_allowed_count' => AssignmentStudentAccess::where('assignment_id', $id)
                    ->where('status', 'allowed')
                    ->count()
            ]);
            
            return view('admin.todo.assignment-show', compact('assignment', 'encryptedId'));
            
        } catch (\Exception $e) {
            \Log::error('Error showing assignment: ' . $e->getMessage());
            
            return redirect()->route('admin.todo.index', ['type' => 'assignment'])
                ->with('error', 'Assignment not found.');
        }
    }

    // ══════════════════════════════════════════════════════════════════
    //  QUIZ SHOW — unified quiz details page with access modal
    // ══════════════════════════════════════════════════════════════════

    /**
     * Show unified quiz details page with access modal
     */
    public function quizShow(string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            $quiz = Quiz::with([
                    'questions.options',
                    'creator',
                    'attempts.user'
                ])
                ->withCount([
                    'studentAccess as allowed_students_count' => function($query) {
                        $query->where('status', 'allowed');
                    },
                    'attempts as attempts_count'
                ])
                ->findOrFail($id);
            
            // Calculate total points
            $totalPoints = $quiz->questions->sum('points');
            
            // Get recent attempts
            $recentAttempts = $quiz->attempts()
                ->with('user')
                ->whereNotNull('completed_at')
                ->latest()
                ->take(10)
                ->get();
            
            // Calculate stats
            $avgScore = $quiz->attempts()
                ->whereNotNull('percentage')
                ->avg('percentage');
            
            $passCount = $quiz->attempts()
                ->where('passed', true)
                ->count();
            
            $failCount = $quiz->attempts()
                ->where('passed', false)
                ->whereNotNull('completed_at')
                ->count();
            
            return view('admin.todo.quiz-show', compact(
                'quiz', 
                'encryptedId', 
                'totalPoints',
                'recentAttempts',
                'avgScore',
                'passCount',
                'failCount'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error showing quiz: ' . $e->getMessage());
            
            return redirect()->route('admin.todo.index', ['type' => 'quiz'])
                ->with('error', 'Quiz not found.');
        }
    }

    /**
     * Get quiz access modal content
     */
    public function quizAccessModal(Request $request, string $encryptedId)
    {
        $id         = Crypt::decrypt($encryptedId);
        $quiz       = Quiz::findOrFail($id);

        $collegeId  = $request->get('college_id');
        $programId  = $request->get('program_id');
        $year       = $request->get('year');
        $searchName = $request->get('search_name');

        $studentQuery = User::where('role', 4)
            ->where('is_approved', 1)
            ->whereNotNull('email_verified_at')
            ->with(['college', 'program']);

        if ($collegeId) {
            $studentQuery->where('college_id', $collegeId);
        }
        
        if ($programId) {
            $studentQuery->where('program_id', $programId);
        }
        
        if ($year) {
            $studentQuery->where('college_year', $year);
        }
        
        if ($searchName) {
            $studentQuery->where(function ($q) use ($searchName) {
                $q->where('f_name', 'like', "%{$searchName}%")
                ->orWhere('l_name', 'like', "%{$searchName}%")
                ->orWhere('student_id', 'like', "%{$searchName}%")
                ->orWhere('email', 'like', "%{$searchName}%");
            });
        }

        $students = $studentQuery->orderBy('l_name')->paginate(10);

        $accessMap = QuizStudentAccess::where('quiz_id', $id)
            ->where('status', 'allowed')
            ->pluck('status', 'student_id');

        $attemptMap = QuizAttempt::where('quiz_id', $id)
            ->whereNotNull('completed_at')
            ->get()
            ->keyBy('user_id');

        foreach ($students as $student) {
            $student->access_status = $accessMap[$student->id] ?? null;
            $attempt = $attemptMap[$student->id] ?? null;
            $student->attempt_status = $attempt ? ($attempt->passed ? 'passed' : 'failed') : null;
            $student->attempt_score = $attempt ? $attempt->percentage : null;
            $student->attempt_date = $attempt ? $attempt->completed_at : null;
        }

        $colleges = College::where('status', 1)->orderBy('college_name')->get();
        $programs = $collegeId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years    = User::where('role', 4)->whereNotNull('college_year')
                        ->distinct()->pluck('college_year')->sort()->values();

        if ($request->ajax()) {
            return view('admin.todo.partials.quiz-access-modal', compact(
                'quiz', 'encryptedId', 'students', 'colleges', 'programs', 'years',
                'collegeId', 'programId', 'year', 'searchName'
            ))->render();
        }

        return view('admin.todo.partials.quiz-access-modal', compact(
            'quiz', 'encryptedId', 'students', 'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName'
        ));
    }

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        $stats = [
            'total_quizzes' => Quiz::count(),
            'total_assignments' => Assignment::count(),
            'total_access_grants' => QuizStudentAccess::where('status', 'allowed')->count() 
                                    + AssignmentStudentAccess::where('status', 'allowed')->count(),
            'pending_reviews' => AssignmentSubmission::whereIn('status', ['submitted', 'late'])->count(),
            'recent_activities' => $this->getRecentActivities()
        ];

        return response()->json($stats);
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($limit = 10)
    {
        $activities = collect();

        // Get recent quiz attempts
        $quizAttempts = QuizAttempt::with(['user', 'quiz'])
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->take($limit)
            ->get()
            ->map(function ($attempt) {
                return [
                    'type' => 'quiz_attempt',
                    'user' => $attempt->user ? $attempt->user->full_name : 'Unknown',
                    'item' => $attempt->quiz ? $attempt->quiz->title : 'Unknown Quiz',
                    'score' => $attempt->percentage . '%',
                    'passed' => $attempt->passed,
                    'date' => $attempt->completed_at
                ];
            });

        // Get recent assignment submissions
        $submissions = AssignmentSubmission::with(['student', 'assignment'])
            ->latest('submitted_at')
            ->take($limit)
            ->get()
            ->map(function ($submission) {
                return [
                    'type' => 'submission',
                    'user' => $submission->student ? $submission->student->full_name : 'Unknown',
                    'item' => $submission->assignment ? $submission->assignment->title : 'Unknown Assignment',
                    'status' => $submission->status,
                    'date' => $submission->submitted_at
                ];
            });

        // Merge and sort by date
        $activities = $quizAttempts->concat($submissions)
            ->sortByDesc('date')
            ->take($limit)
            ->values();

        return $activities;
    }

    /**
     * Clear todo-related caches
     */
    public function clearCache()
    {
        Cache::flush();
        
        return redirect()->back()
            ->with('success', 'All todo caches cleared successfully.');
    }
}