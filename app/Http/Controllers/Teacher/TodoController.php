<?php

namespace App\Http\Controllers\Teacher;

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

class TodoController extends Controller
{
    /**
     * Display a listing of quizzes and assignments created by the teacher
     */
    public function index(Request $request)
    {
        $teacherId = auth()->id();
        $type = $request->get('type', 'all');
        $search = $request->get('search', '');

        $quizzes = collect();
        $assignments = collect();

        // Get ONLY quizzes created by this teacher
        if ($type === 'all' || $type === 'quiz') {
            $quizQuery = Quiz::where('created_by', $teacherId)
                ->with(['creator'])
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

        // Get ONLY assignments created by this teacher
        if ($type === 'all' || $type === 'assignment') {
            $assignQuery = Assignment::where('created_by', $teacherId)
                ->with(['course', 'creator'])
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

        // Calculate totals from teacher's quizzes and assignments
        $totalQuizzes = Quiz::where('created_by', $teacherId)->count();
        $totalAssignments = Assignment::where('created_by', $teacherId)->count();
        
        // Calculate total access grants from teacher's quizzes and assignments
        $quizIds = Quiz::where('created_by', $teacherId)->pluck('id');
        $assignmentIds = Assignment::where('created_by', $teacherId)->pluck('id');
        
        $totalAccess = QuizStudentAccess::whereIn('quiz_id', $quizIds)
                ->where('status', 'allowed')->count()
            + AssignmentStudentAccess::whereIn('assignment_id', $assignmentIds)
                ->where('status', 'allowed')->count();

        // Calculate pending reviews from teacher's items
        $pendingReviews = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
                ->whereIn('status', ['submitted', 'late'])->count()
            + QuizAttempt::whereIn('quiz_id', $quizIds)
                ->whereNotNull('completed_at')
                ->where('passed', false)->count();

        return view('teacher.todo.index', compact(
            'quizzes', 'assignments', 'type', 'search',
            'totalQuizzes', 'totalAssignments', 'totalAccess', 'pendingReviews'
        ));
    }

    /**
     * Show unified quiz details page with access modal
     */
    public function quizShow(string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Find the quiz - allow viewing any quiz (for when accessed from courses/progress)
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
            
            return view('teacher.todo.quiz-show', compact(
                'quiz', 
                'encryptedId', 
                'totalPoints',
                'recentAttempts',
                'avgScore',
                'passCount',
                'failCount'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Teacher error showing quiz: ' . $e->getMessage(), [
                'encryptedId' => $encryptedId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('teacher.todo.index', ['type' => 'quiz'])
                ->with('error', 'Quiz not found.');
        }
    }

    /**
     * Show unified assignment details page with access modal
     */
    public function assignmentShow(string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Find the assignment - allow viewing any assignment (for when accessed from courses/progress)
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
            
            return view('teacher.todo.assignment-show', compact('assignment', 'encryptedId'));
            
        } catch (\Exception $e) {
            \Log::error('Teacher error showing assignment: ' . $e->getMessage());
            
            return redirect()->route('teacher.todo.index', ['type' => 'assignment'])
                ->with('error', 'Assignment not found.');
        }
    }

    /**
     * Quiz access management - only for teacher's own quizzes
     */
    public function quizAccess(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Verify teacher created this quiz
            $quiz = Quiz::where('created_by', auth()->id())
                ->with(['creator'])
                ->withCount(['studentAccess as allowed_count' => fn($q) => $q->where('status', 'allowed')])
                ->with('attempts')
                ->findOrFail($id);

            $collegeId = $request->get('college_id');
            $programId = $request->get('program_id');
            $year = $request->get('year');
            $searchName = $request->get('search_name');

            $studentQuery = User::where('role', 4)
                ->where('is_approved', 1)
                ->whereNotNull('email_verified_at')
                ->with(['college', 'program']);

            if ($collegeId) $studentQuery->where('college_id', $collegeId);
            if ($programId) $studentQuery->where('program_id', $programId);
            if ($year) $studentQuery->where('college_year', $year);
            if ($searchName) {
                $studentQuery->where(function ($q) use ($searchName) {
                    $q->where('f_name', 'like', "%{$searchName}%")
                        ->orWhere('l_name', 'like', "%{$searchName}%")
                        ->orWhere('student_id', 'like', "%{$searchName}%")
                        ->orWhere('email', 'like', "%{$searchName}%");
                });
            }

            $students = $studentQuery->orderBy('l_name')->paginate(20)->withQueryString();

            $accessMap = QuizStudentAccess::where('quiz_id', $id)
                ->pluck('status', 'student_id');

            foreach ($students as $student) {
                $student->access_status = $accessMap[$student->id] ?? null;
            }

            $colleges = College::where('status', 1)->orderBy('college_name')->get();
            $programs = $collegeId
                ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
                : collect();
            $years = User::where('role', 4)->whereNotNull('college_year')
                ->distinct()->pluck('college_year')->sort()->values();

            return view('teacher.todo.quiz-access', compact(
                'quiz', 'encryptedId', 'students', 'colleges', 'programs', 'years',
                'collegeId', 'programId', 'year', 'searchName'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Teacher error in quizAccess: ' . $e->getMessage());
            
            return redirect()->route('teacher.todo.index', ['type' => 'quiz'])
                ->with('error', 'Failed to load quiz access management.');
        }
    }

    /**
     * Assignment access management - only for teacher's own assignments
     */
    public function assignmentAccess(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Verify teacher created this assignment
            $assignment = Assignment::where('created_by', auth()->id())
                ->with(['creator'])
                ->withCount(['allowedStudents as allowed_count'])
                ->withCount('submissions')
                ->findOrFail($id);

            $collegeId = $request->get('college_id');
            $programId = $request->get('program_id');
            $year = $request->get('year');
            $searchName = $request->get('search_name');

            $studentQuery = User::where('role', 4)
                ->where('is_approved', 1)
                ->whereNotNull('email_verified_at')
                ->with(['college', 'program']);

            if ($collegeId) $studentQuery->where('college_id', $collegeId);
            if ($programId) $studentQuery->where('program_id', $programId);
            if ($year) $studentQuery->where('college_year', $year);
            if ($searchName) {
                $studentQuery->where(function ($q) use ($searchName) {
                    $q->where('f_name', 'like', "%{$searchName}%")
                        ->orWhere('l_name', 'like', "%{$searchName}%")
                        ->orWhere('student_id', 'like', "%{$searchName}%");
                });
            }

            $students = $studentQuery->orderBy('l_name')->paginate(20)->withQueryString();

            $accessMap = AssignmentStudentAccess::where('assignment_id', $id)
                ->pluck('status', 'student_id');

            $submissionMap = AssignmentSubmission::where('assignment_id', $id)
                ->pluck('status', 'student_id');

            foreach ($students as $student) {
                $student->access_status = $accessMap[$student->id] ?? null;
                $student->submission_status = $submissionMap[$student->id] ?? null;
            }

            $colleges = College::where('status', 1)->orderBy('college_name')->get();
            $programs = $collegeId
                ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
                : collect();
            $years = User::where('role', 4)->whereNotNull('college_year')
                ->distinct()->pluck('college_year')->sort()->values();

            return view('teacher.todo.assignment-access', compact(
                'assignment', 'encryptedId', 'students', 'colleges', 'programs', 'years',
                'collegeId', 'programId', 'year', 'searchName'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Teacher error in assignmentAccess: ' . $e->getMessage());
            
            return redirect()->route('teacher.todo.index', ['type' => 'assignment'])
                ->with('error', 'Failed to load assignment access management.');
        }
    }

    /**
     * Get quiz access modal content (AJAX) - only for teacher's own quizzes
     */
    public function quizAccessModal(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Verify teacher created this quiz
            $quiz = Quiz::where('created_by', auth()->id())->with('creator')->findOrFail($id);

            $collegeId = $request->get('college_id');
            $programId = $request->get('program_id');
            $year = $request->get('year');
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
            $years = User::where('role', 4)->whereNotNull('college_year')
                            ->distinct()->pluck('college_year')->sort()->values();

            if ($request->ajax()) {
                return view('teacher.todo.partials.quiz-access-modal', compact(
                    'quiz', 'encryptedId', 'students', 'colleges', 'programs', 'years',
                    'collegeId', 'programId', 'year', 'searchName'
                ))->render();
            }

            return view('teacher.todo.partials.quiz-access-modal', compact(
                'quiz', 'encryptedId', 'students', 'colleges', 'programs', 'years',
                'collegeId', 'programId', 'year', 'searchName'
            ));

        } catch (\Exception $e) {
            \Log::error('Teacher error loading quiz access modal: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to load students'], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to load access management.');
        }
    }

    /**
     * Get assignment access modal content (AJAX) - only for teacher's own assignments
     */
    public function assignmentAccessModal(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Verify teacher created this assignment
            $assignment = Assignment::where('created_by', auth()->id())->with('creator')->findOrFail($id);

            $collegeId = $request->get('college_id');
            $programId = $request->get('program_id');
            $year = $request->get('year');
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
                $student->access_status = $accessMap[$student->id] ?? null;
                $student->submission_status = $submissionMap[$student->id] ?? null;
            }

            $colleges = College::where('status', 1)->orderBy('college_name')->get();
            $programs = $collegeId
                ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
                : collect();
            $years = User::where('role', 4)->whereNotNull('college_year')
                            ->distinct()->pluck('college_year')->sort()->values();

            if ($request->ajax()) {
                return view('teacher.todo.partials.assignment-access-modal', compact(
                    'assignment', 'encryptedId', 'students', 'colleges', 'programs', 'years',
                    'collegeId', 'programId', 'year', 'searchName'
                ))->render();
            }

            return view('teacher.todo.partials.assignment-access-modal', compact(
                'assignment', 'encryptedId', 'students', 'colleges', 'programs', 'years',
                'collegeId', 'programId', 'year', 'searchName'
            ));

        } catch (\Exception $e) {
            \Log::error('Teacher error loading assignment access modal: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to load students'], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to load access management.');
        }
    }

    /**
     * Grant quiz access to students - only for teacher's own quizzes
     */
    public function grantQuizAccess(Request $request, string $encryptedId)
    {
        $quizId = Crypt::decrypt($encryptedId);
        
        // Verify teacher created this quiz
        $quiz = Quiz::where('created_by', auth()->id())->findOrFail($quizId);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->student_ids as $studentId) {
            QuizStudentAccess::updateOrCreate(
                ['quiz_id' => $quizId, 'student_id' => $studentId],
                [
                    'status' => 'allowed',
                    'granted_by' => auth()->id(),
                    'granted_at' => now(),
                ]
            );
        }

        Cache::forget('student_todo_' . implode(',', $request->student_ids));

        return back()->with('success', count($request->student_ids) . ' student(s) granted access to quiz.');
    }

    /**
     * Grant assignment access to students - only for teacher's own assignments
     */
    public function grantAssignmentAccess(Request $request, string $encryptedId)
    {
        $assignmentId = Crypt::decrypt($encryptedId);
        
        // Verify teacher created this assignment
        $assignment = Assignment::where('created_by', auth()->id())->findOrFail($assignmentId);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->student_ids as $studentId) {
            AssignmentStudentAccess::updateOrCreate(
                ['assignment_id' => $assignmentId, 'student_id' => $studentId],
                [
                    'status' => 'allowed',
                    'granted_by' => auth()->id(),
                    'granted_at' => now(),
                ]
            );
        }

        return back()->with('success', count($request->student_ids) . ' student(s) granted access to assignment.');
    }

    /**
     * Revoke quiz access from students - only for teacher's own quizzes
     */
    public function revokeQuizAccess(Request $request, string $encryptedId)
    {
        $quizId = Crypt::decrypt($encryptedId);
        
        // Verify teacher created this quiz
        $quiz = Quiz::where('created_by', auth()->id())->findOrFail($quizId);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        QuizStudentAccess::where('quiz_id', $quizId)
            ->whereIn('student_id', $request->student_ids)
            ->update(['status' => 'revoked']);

        return back()->with('success', count($request->student_ids) . ' student(s) revoked from quiz.');
    }

    /**
     * Revoke assignment access from students - only for teacher's own assignments
     */
    public function revokeAssignmentAccess(Request $request, string $encryptedId)
    {
        $assignmentId = Crypt::decrypt($encryptedId);
        
        // Verify teacher created this assignment
        $assignment = Assignment::where('created_by', auth()->id())->findOrFail($assignmentId);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        AssignmentStudentAccess::where('assignment_id', $assignmentId)
            ->whereIn('student_id', $request->student_ids)
            ->update(['status' => 'revoked']);

        return back()->with('success', count($request->student_ids) . ' student(s) revoked from assignment.');
    }

    /**
     * Toggle individual quiz access - only for teacher's own quizzes
     */
    public function toggleQuizAccess(string $encryptedId, int $studentId)
    {
        $quizId = Crypt::decrypt($encryptedId);
        
        // Verify teacher created this quiz
        $quiz = Quiz::where('created_by', auth()->id())->findOrFail($quizId);

        $access = QuizStudentAccess::where('quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->first();

        if (!$access) {
            QuizStudentAccess::create([
                'quiz_id' => $quizId,
                'student_id' => $studentId,
                'status' => 'allowed',
                'granted_by' => auth()->id(),
                'granted_at' => now(),
            ]);
            $newStatus = 'allowed';
        } else {
            $newStatus = $access->status === 'allowed' ? 'revoked' : 'allowed';
            $access->update([
                'status' => $newStatus,
                'granted_by' => auth()->id(),
                'granted_at' => $newStatus === 'allowed' ? now() : $access->granted_at,
            ]);
        }

        return response()->json(['status' => $newStatus, 'message' => 'Access updated.']);
    }

    /**
     * Toggle individual assignment access - only for teacher's own assignments
     */
    public function toggleAssignmentAccess(string $encryptedId, int $studentId)
    {
        $assignmentId = Crypt::decrypt($encryptedId);
        
        // Verify teacher created this assignment
        $assignment = Assignment::where('created_by', auth()->id())->findOrFail($assignmentId);

        $access = AssignmentStudentAccess::where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();

        if (!$access) {
            AssignmentStudentAccess::create([
                'assignment_id' => $assignmentId,
                'student_id' => $studentId,
                'status' => 'allowed',
                'granted_by' => auth()->id(),
                'granted_at' => now(),
            ]);
            $newStatus = 'allowed';
        } else {
            $newStatus = $access->status === 'allowed' ? 'revoked' : 'allowed';
            $access->update([
                'status' => $newStatus,
                'granted_by' => auth()->id(),
                'granted_at' => $newStatus === 'allowed' ? now() : $access->granted_at,
            ]);
        }

        return response()->json(['status' => $newStatus, 'message' => 'Access updated.']);
    }

    /**
     * View progress of teacher's quizzes and assignments
     */
    public function progress(Request $request)
    {
        $teacherId = auth()->id();
        $type = $request->get('type', 'quiz');
        $collegeId = $request->get('college_id');
        $programId = $request->get('program_id');
        $year = $request->get('year');
        $searchName = $request->get('search_name');
        $itemId = $request->get('item_id');

        // Get teacher's quiz and assignment IDs
        $quizIds = Quiz::where('created_by', $teacherId)->pluck('id');
        $assignmentIds = Assignment::where('created_by', $teacherId)->pluck('id');

        $quizProgress = collect();
        $assignmentProgress = collect();

        if ($type === 'quiz') {
            $query = QuizAttempt::with(['user.college', 'user.program', 'quiz'])
                ->whereIn('quiz_id', $quizIds)
                ->whereHas('user', function ($q) use ($collegeId, $programId, $year, $searchName) {
                    $q->where('role', 4);
                    if ($collegeId) $q->where('college_id', $collegeId);
                    if ($programId) $q->where('program_id', $programId);
                    if ($year) $q->where('college_year', $year);
                    if ($searchName) {
                        $q->where(function ($sq) use ($searchName) {
                            $sq->where('f_name', 'like', "%{$searchName}%")
                                ->orWhere('l_name', 'like', "%{$searchName}%");
                        });
                    }
                });

            if ($itemId) $query->where('quiz_id', $itemId);

            $quizProgress = $query->latest('completed_at')->paginate(25)->withQueryString();
        }

        if ($type === 'assignment') {
            $query = AssignmentSubmission::with(['student.college', 'student.program', 'assignment'])
                ->whereIn('assignment_id', $assignmentIds)
                ->whereHas('student', function ($q) use ($collegeId, $programId, $year, $searchName) {
                    $q->where('role', 4);
                    if ($collegeId) $q->where('college_id', $collegeId);
                    if ($programId) $q->where('program_id', $programId);
                    if ($year) $q->where('college_year', $year);
                    if ($searchName) {
                        $q->where(function ($sq) use ($searchName) {
                            $sq->where('f_name', 'like', "%{$searchName}%")
                                ->orWhere('l_name', 'like', "%{$searchName}%");
                        });
                    }
                });

            if ($itemId) $query->where('assignment_id', $itemId);

            $assignmentProgress = $query->latest('submitted_at')->paginate(25)->withQueryString();
        }

        $colleges = College::where('status', 1)->orderBy('college_name')->get();
        $programs = $collegeId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years = User::where('role', 4)->whereNotNull('college_year')
            ->distinct()->pluck('college_year')->sort()->values();
        
        // Get ONLY teacher's quizzes and assignments for filters
        $quizList = Quiz::where('created_by', $teacherId)->orderBy('title')->get(['id', 'title']);
        $assignList = Assignment::where('created_by', $teacherId)->orderBy('title')->get(['id', 'title']);

        return view('teacher.todo.progress', compact(
            'type', 'quizProgress', 'assignmentProgress',
            'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName', 'itemId',
            'quizList', 'assignList'
        ));
    }

    /**
     * View single assignment submission - only for teacher's own assignments
     */
    public function viewSubmission(int $submissionId)
    {
        $teacherId = auth()->id();
        
        $submission = AssignmentSubmission::with(['student', 'assignment', 'gradedBy'])
            ->whereHas('assignment', function ($q) use ($teacherId) {
                $q->where('created_by', $teacherId);
            })
            ->findOrFail($submissionId);

        return view('teacher.todo.submission-detail', compact('submission'));
    }

    /**
     * Grade a submission - only for teacher's own assignments
     */
    public function gradeSubmission(Request $request, int $submissionId)
    {
        $teacherId = auth()->id();
        
        $submission = AssignmentSubmission::with('assignment')
            ->whereHas('assignment', function ($q) use ($teacherId) {
                $q->where('created_by', $teacherId);
            })
            ->findOrFail($submissionId);

        $request->validate([
            'score' => 'required|integer|min:0|max:' . ($submission->assignment->points ?? 100),
            'feedback' => 'nullable|string|max:2000',
        ]);

        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'status' => 'graded',
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Submission graded successfully.');
    }

    /**
     * Get programs by college (AJAX helper)
     */
    public function getProgramsByCollege(int $collegeId)
    {
        $programs = Program::where('college_id', $collegeId)
            ->orderBy('program_name')
            ->get(['id', 'program_name']);
        return response()->json($programs);
    }
}