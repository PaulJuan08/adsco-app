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
use Illuminate\Support\Facades\DB;

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

        // Get quizzes created by this teacher
        if ($type === 'all' || $type === 'quiz') {
            $quizQuery = Quiz::where('created_by', $teacherId)
                ->withCount(['studentAccess as allowed_students_count' => function ($q) {
                    $q->where('status', 'allowed');
                }])
                ->withCount('attempts');

            if ($search) {
                $quizQuery->where('title', 'like', "%{$search}%");
            }

            $quizzes = $quizQuery->latest()->get();
        }

        // Get assignments created by this teacher
        if ($type === 'all' || $type === 'assignment') {
            $assignQuery = Assignment::where('created_by', $teacherId)
                ->withCount(['allowedStudents as allowed_students_count'])
                ->withCount('submissions');

            if ($search) {
                $assignQuery->where('title', 'like', "%{$search}%");
            }

            $assignments = $assignQuery->latest()->get();
        }

        $totalQuizzes = Quiz::where('created_by', $teacherId)->count();
        $totalAssignments = Assignment::where('created_by', $teacherId)->count();
        
        $totalAccess = QuizStudentAccess::whereIn('quiz_id', 
                Quiz::where('created_by', $teacherId)->pluck('id')
            )->where('status', 'allowed')->count()
            + AssignmentStudentAccess::whereIn('assignment_id', 
                Assignment::where('created_by', $teacherId)->pluck('id')
            )->where('status', 'allowed')->count();

        return view('teacher.todo.index', compact(
            'quizzes', 'assignments', 'type', 'search',
            'totalQuizzes', 'totalAssignments', 'totalAccess'
        ));
    }

    /**
     * Quiz access management
     */
    public function quizAccess(Request $request, string $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        
        // Verify teacher created this quiz
        $quiz = Quiz::where('created_by', auth()->id())
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
        $programs = $programId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years = User::where('role', 4)->whereNotNull('college_year')
            ->distinct()->pluck('college_year')->sort()->values();

        return view('teacher.todo.quiz-access', compact(
            'quiz', 'encryptedId', 'students', 'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName'
        ));
    }

    /**
     * Grant quiz access to students
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
     * Revoke quiz access from students
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
     * Toggle individual quiz access
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
     * Assignment access management
     */
    public function assignmentAccess(Request $request, string $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        
        // Verify teacher created this assignment
        $assignment = Assignment::where('created_by', auth()->id())
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
        $programs = $programId
            ? Program::where('college_id', $collegeId)->orderBy('program_name')->get()
            : collect();
        $years = User::where('role', 4)->whereNotNull('college_year')
            ->distinct()->pluck('college_year')->sort()->values();

        return view('teacher.todo.assignment-access', compact(
            'assignment', 'encryptedId', 'students', 'colleges', 'programs', 'years',
            'collegeId', 'programId', 'year', 'searchName'
        ));
    }

    /**
     * Grant assignment access to students
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
     * Revoke assignment access from students
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
     * Toggle individual assignment access
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
     * View progress of all quizzes and assignments
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

        $quizProgress = collect();
        $assignmentProgress = collect();

        if ($type === 'quiz') {
            $query = QuizAttempt::with(['user.college', 'user.program', 'quiz'])
                ->whereHas('quiz', function ($q) use ($teacherId) {
                    $q->where('created_by', $teacherId);
                })
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
                ->whereHas('assignment', function ($q) use ($teacherId) {
                    $q->where('created_by', $teacherId);
                })
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
     * View single assignment submission
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
     * Grade a submission
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