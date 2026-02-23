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
            $quizQuery = Quiz::withCount(['studentAccess as allowed_students_count' => function ($q) {
                $q->where('status', 'allowed');
            }])->withCount('attempts');

            if ($search) {
                $quizQuery->where('title', 'like', "%{$search}%");
            }

            $quizzes = $quizQuery->latest()->get();
        }

        if ($type === 'all' || $type === 'assignment') {
            $assignQuery = Assignment::with('course')
                ->withCount(['allowedStudents as allowed_students_count'])
                ->withCount('submissions');

            if ($search) {
                $assignQuery->where('title', 'like', "%{$search}%");
            }

            $assignments = $assignQuery->latest()->get();
        }

        $totalQuizzes      = Quiz::count();
        $totalAssignments  = Assignment::count();
        $totalAccess       = QuizStudentAccess::where('status', 'allowed')->count()
                           + AssignmentStudentAccess::where('status', 'allowed')->count();

        return view('admin.todo.index', compact(
            'quizzes', 'assignments', 'type', 'search',
            'totalQuizzes', 'totalAssignments', 'totalAccess'
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

        // DEBUG: Let's see what students exist in the database
        \Log::info('=== DEBUG: Quiz Access Query ===');
        \Log::info('Total users with role=4: ' . User::where('role', 4)->count());
        \Log::info('Total approved students: ' . User::where('role', 4)->where('is_approved', 1)->count());
        \Log::info('Total verified students: ' . User::where('role', 4)->whereNotNull('email_verified_at')->count());

        $studentQuery = User::where('role', 4);
        
        // REMOVE these restrictions temporarily to see all students
        // ->where('is_approved', 1)
        // ->whereNotNull('email_verified_at')
        
        // Add with counts to see relationships
        $studentQuery->with(['college', 'program']);

        if ($collegeId) $studentQuery->where('college_id', $collegeId);
        if ($programId) $studentQuery->where('program_id', $programId);
        if ($year)      $studentQuery->where('college_year', $year);
        if ($searchName) {
            $studentQuery->where(function ($q) use ($searchName) {
                $q->where('f_name', 'like', "%{$searchName}%")
                ->orWhere('l_name', 'like', "%{$searchName}%")
                ->orWhere('student_id', 'like', "%{$searchName}%")
                ->orWhere('email', 'like', "%{$searchName}%");
            });
        }

        // Log the SQL query
        \Log::info('SQL Query: ' . $studentQuery->toSql());
        \Log::info('Bindings: ' . json_encode($studentQuery->getBindings()));

        $students = $studentQuery->orderBy('l_name')->paginate(20)->withQueryString();

        \Log::info('Students found: ' . $students->total());

        // Attach access status to each student
        $accessMap = QuizStudentAccess::where('quiz_id', $id)
            ->pluck('status', 'student_id');

        foreach ($students as $student) {
            $student->access_status = $accessMap[$student->id] ?? null;
            
            // Debug: Log student info
            \Log::info('Student: ' . $student->id . ' - ' . $student->f_name . ' ' . $student->l_name . 
                    ' - College: ' . ($student->college->college_name ?? 'None') . 
                    ' - Approved: ' . ($student->is_approved ? 'Yes' : 'No') . 
                    ' - Verified: ' . ($student->email_verified_at ? 'Yes' : 'No'));
        }

        $colleges = College::where('status', 1)->orderBy('college_name')->get();
        $programs = $programId
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
        $id         = Crypt::decrypt($encryptedId);
        $assignment = Assignment::with('course')
            ->withCount(['allowedStudents as allowed_count'])
            ->withCount('submissions')
            ->findOrFail($id);

        $collegeId  = $request->get('college_id');
        $programId  = $request->get('program_id');
        $year       = $request->get('year');
        $searchName = $request->get('search_name');

        $studentQuery = User::where('role', 4)
            ->where('is_approved', 1)
            ->whereNotNull('email_verified_at')
            ->with(['college', 'program']);

        if ($collegeId)  $studentQuery->where('college_id', $collegeId);
        if ($programId)  $studentQuery->where('program_id', $programId);
        if ($year)       $studentQuery->where('college_year', $year);
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
            $student->access_status     = $accessMap[$student->id] ?? null;
            $student->submission_status = $submissionMap[$student->id] ?? null;
        }

        $colleges = College::where('status', 1)->orderBy('college_name')->get();
        $programs = $programId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years    = User::where('role', 4)->whereNotNull('college_year')
                        ->distinct()->pluck('college_year')->sort()->values();

        return view('admin.todo.assignment-access', compact(
            'assignment', 'encryptedId', 'students', 'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName'
        ));
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
                    if ($collegeId)  $q->where('college_id', $collegeId);
                    if ($programId)  $q->where('program_id', $programId);
                    if ($year)       $q->where('college_year', $year);
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
                ->whereHas('student', function ($q) use ($collegeId, $programId, $year, $searchName) {
                    $q->where('role', 4);
                    if ($collegeId)  $q->where('college_id', $collegeId);
                    if ($programId)  $q->where('program_id', $programId);
                    if ($year)       $q->where('college_year', $year);
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
    //  VIEW single submission (assignment)
    // ══════════════════════════════════════════════════════════════════

    public function viewSubmission(int $submissionId)
    {
        $submission = AssignmentSubmission::with(['student', 'assignment', 'gradedBy'])
            ->findOrFail($submissionId);

        return view('admin.todo.submission-detail', compact('submission'));
    }

    public function gradeSubmission(Request $request, int $submissionId)
    {
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

        return back()->with('success', 'Submission graded successfully.');
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
}