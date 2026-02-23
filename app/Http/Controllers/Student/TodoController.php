<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentStudentAccess;
use App\Models\AssignmentSubmission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizStudentAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $studentId = Auth::id();
        $type = $request->get('type', 'all');

        // Get quizzes the student has access to
        $allowedQuizIds = QuizStudentAccess::where('student_id', $studentId)
            ->where('status', 'allowed')
            ->pluck('quiz_id');

        $quizzes = collect();
        if ($type === 'all' || $type === 'quiz') {
            $quizzes = Quiz::whereIn('id', $allowedQuizIds)
                ->where('is_published', 1)
                ->withCount('questions')
                ->get()
                ->map(function ($quiz) use ($studentId) {
                    $latestAttempt = QuizAttempt::where('quiz_id', $quiz->id)
                        ->where('user_id', $studentId)
                        ->whereNotNull('completed_at')
                        ->latest('completed_at')
                        ->first();
                    
                    $quiz->latest_attempt = $latestAttempt;
                    $quiz->attempt_count = QuizAttempt::where('quiz_id', $quiz->id)
                        ->where('user_id', $studentId)
                        ->whereNotNull('completed_at')
                        ->count();
                    
                    return $quiz;
                });
        }

        // Get assignments the student has access to
        $allowedAssignmentIds = AssignmentStudentAccess::where('student_id', $studentId)
            ->where('status', 'allowed')
            ->pluck('assignment_id');

        $assignments = collect();
        if ($type === 'all' || $type === 'assignment') {
            $assignments = Assignment::whereIn('id', $allowedAssignmentIds)
                ->where('is_published', 1)
                ->with('course')
                ->get()
                ->map(function ($assignment) use ($studentId) {
                    $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                        ->where('student_id', $studentId)
                        ->latest()
                        ->first();
                    
                    $assignment->my_submission = $submission;
                    return $assignment;
                });
        }

        return view('student.todo.index', compact('quizzes', 'assignments', 'type'));
    }

    public function takeQuiz(string $encryptedId)
    {
        $studentId = Auth::id();
        $quizId = Crypt::decrypt($encryptedId);

        abort_unless(
            QuizStudentAccess::where('quiz_id', $quizId)
                ->where('student_id', $studentId)
                ->where('status', 'allowed')
                ->exists(),
            403
        );

        $quiz = Quiz::with(['questions.options' => function ($q) {
            $q->select(['id', 'quiz_question_id', 'option_text', 'order']);
        }])->findOrFail($quizId);

        abort_if(!$quiz->is_published, 403);

        // Check if there's an existing attempt
        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('user_id', $studentId)
            ->first();

        if ($attempt) {
            // Update existing attempt with new start time
            $attempt->update([
                'started_at' => now(),
                'completed_at' => null, // Reset completion
                'score' => null,
                'total_points' => null,
                'percentage' => null,
                'passed' => 0,
                'answers' => null,
            ]);
        } else {
            // Create new attempt
            $attempt = QuizAttempt::create([
                'quiz_id' => $quizId,
                'user_id' => $studentId,
                'total_questions' => $quiz->questions->count(),
                'started_at' => now(),
            ]);
        }

        return view('student.todo.take-quiz', compact('quiz', 'encryptedId'));
    }

    public function submitQuiz(Request $request, string $encryptedId)
    {
        $studentId = Auth::id();
        $quizId = Crypt::decrypt($encryptedId);

        abort_unless(
            QuizStudentAccess::where('quiz_id', $quizId)
                ->where('student_id', $studentId)
                ->where('status', 'allowed')
                ->exists(),
            403
        );

        $quiz = Quiz::with(['questions.options' => function($q) {
            $q->where('is_correct', true);
        }])->findOrFail($quizId);

        $score = 0;
        $totalPoints = 0;
        $results = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += 1;
            $userAnswer = $request->input("question_{$question->id}");
            $correctOption = $question->options->where('is_correct', true)->first();
            $isCorrect = $correctOption && $userAnswer == $correctOption->id;

            if ($isCorrect) $score++;

            $results[] = [
                'question' => $question->question,
                'user_answer' => $userAnswer,
                'correct_option' => $correctOption?->id,
                'is_correct' => $isCorrect,
            ];
        }

        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;
        $passed = $percentage >= $quiz->passing_score;

        // Check if there's an existing attempt for this quiz and user
        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('user_id', $studentId)
            ->first();

        if ($attempt) {
            // UPDATE existing record
            $attempt->update([
                'score' => $score,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'passed' => $passed ? 1 : 0,
                'answers' => json_encode($results),
                'completed_at' => now(),
                'time_taken' => $attempt->started_at ? now()->diffInSeconds($attempt->started_at) : null,
            ]);
        } else {
            // CREATE new record (first time only)
            $attempt = QuizAttempt::create([
                'quiz_id' => $quizId,
                'user_id' => $studentId,
                'score' => $score,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'passed' => $passed ? 1 : 0,
                'answers' => json_encode($results),
                'completed_at' => now(),
                'total_questions' => $quiz->questions->count(),
                'started_at' => now(),
                'time_taken' => 0,
            ]);
        }

        return redirect()->route('student.todo.index')
            ->with('quiz_results', [
                'quiz' => $quiz->title,
                'score' => $score,
                'total' => $totalPoints,
                'percentage' => $percentage,
                'passed' => $passed,
            ]);
    }

    public function viewAssignment(string $encryptedId)
    {
        $studentId = Auth::id();
        $assignmentId = Crypt::decrypt($encryptedId);

        abort_unless(
            AssignmentStudentAccess::where('assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->where('status', 'allowed')
                ->exists(),
            403
        );

        $assignment = Assignment::with('course')->findOrFail($assignmentId);
        $submission = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->latest()
            ->first();

        return view('student.todo.view-assignment', compact('assignment', 'encryptedId', 'submission'));
    }

    public function submitAssignment(Request $request, string $encryptedId)
    {
        $studentId = Auth::id();
        $assignmentId = Crypt::decrypt($encryptedId);

        abort_unless(
            AssignmentStudentAccess::where('assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->where('status', 'allowed')
                ->exists(),
            403
        );

        $request->validate([
            'answer_text' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('assignments', 'public');
        }

        $assignment = Assignment::findOrFail($assignmentId);
        $isLate = $assignment->due_date && now()->isAfter($assignment->due_date);

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignmentId, 'student_id' => $studentId],
            [
                'answer_text' => $request->answer_text,
                'attachment_path' => $attachmentPath,
                'status' => $isLate ? 'late' : 'submitted',
                'submitted_at' => now(),
            ]
        );

        return redirect()->route('student.todo.index')->with('success', 'Assignment submitted');
    }
}