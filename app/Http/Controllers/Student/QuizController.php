<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display all available quizzes for student
     */
    public function index()
    {
        $user = Auth::user();
        $now = now();
        
        // Debug: Check if user is authenticated
        if (!$user) {
            \Log::error('No authenticated user in quiz index');
            abort(401, 'Not authenticated');
        }
        
        \Log::info('Quiz index accessed by user: ' . $user->id . ' - ' . $user->email);
        
        // Get all published and available quizzes
        $quizzes = Quiz::where('is_published', 1)
            ->where(function($query) use ($now) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('available_until')
                    ->orWhere('available_until', '>=', $now);
            })
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();
        
        \Log::info('Found ' . $quizzes->count() . ' quizzes');
        
        // Get all attempts by this student
        $attempts = QuizAttempt::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->get()
            ->keyBy('quiz_id');
        
        \Log::info('Found ' . $attempts->count() . ' attempts');
        
        return view('student.quizzes.index', compact('quizzes', 'attempts'));
    }
    
    /**
     * Show quiz with questions and options immediately
     */
    public function show($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $userId = Auth::id();
            
            // Load quiz with questions and their options
            $quiz = Quiz::with(['questions' => function($query) {
                $query->orderBy('order', 'asc')->orderBy('id', 'asc');
            }, 'questions.options' => function($query) {
                $query->orderBy('order', 'asc')->orderBy('id', 'asc');
            }])
            ->where('is_published', 1)
            ->findOrFail($quizId);
            
            \Log::info('Quiz loaded: ' . $quiz->title . ' with ' . $quiz->questions->count() . ' questions');
            
            // Check if quiz is available
            $now = now();
            $isAvailable = true;
            
            if ($quiz->available_from && $quiz->available_from > $now) {
                $isAvailable = false;
            }
            
            if ($quiz->available_until && $quiz->available_until < $now) {
                $isAvailable = false;
            }
            
            if (!$isAvailable) {
                return redirect()->route('student.quizzes.index')
                    ->with('error', 'This quiz is not currently available.');
            }
            
            // Check for existing attempts
            $attempt = QuizAttempt::where('quiz_id', $quizId)
                ->where('user_id', $userId)
                ->whereNull('completed_at')
                ->first();
            
            // If no incomplete attempt, create new one (UNLIMITED ATTEMPTS)
            if (!$attempt) {
                // Create new attempt - NO MAX ATTEMPTS CHECK
                $attempt = new QuizAttempt();
                $attempt->quiz_id = $quizId;
                $attempt->user_id = $userId;
                $attempt->total_questions = $quiz->questions->count();
                $attempt->started_at = now();
                $attempt->answers = [];
                $attempt->save();
                
                \Log::info('Created new attempt for quiz ' . $quizId . ' for user ' . $userId);
            }
            
            // Get user's answers
            $userAnswers = $attempt->answers ?? [];
            
            // Get completed attempt if exists
            $completedAttempt = QuizAttempt::where('quiz_id', $quizId)
                ->where('user_id', $userId)
                ->whereNotNull('completed_at')
                ->latest()
                ->first();
            
            return view('student.quizzes.show', compact('quiz', 'attempt', 'userAnswers', 'completedAttempt'));
            
        } catch (\Exception $e) {
            \Log::error('Error in quiz show: ' . $e->getMessage());
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Quiz not found: ' . $e->getMessage());
        }
    }
    
    /**
     * Submit quiz answers
     */
    public function submit(Request $request, $encryptedId)
    {
        DB::beginTransaction();
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $userId = Auth::id();
            
            $quiz = Quiz::with(['questions.options'])->findOrFail($quizId);
            $attempt = QuizAttempt::where('quiz_id', $quizId)
                ->where('user_id', $userId)
                ->whereNull('completed_at')
                ->firstOrFail();
            
            // Get answers from request
            $answers = $request->input('answers', []);
            $score = 0;
            
            // Calculate score
            foreach ($quiz->questions as $question) {
                if (isset($answers[$question->id])) {
                    $selectedOptionId = $answers[$question->id];
                    $correctOption = $question->options->where('is_correct', 1)->first();
                    
                    if ($correctOption && $selectedOptionId == $correctOption->id) {
                        $score += $question->points ?? 1;
                    }
                }
            }
            
            // Calculate results
            $totalPoints = $quiz->questions->sum('points') ?? $quiz->questions->count();
            $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;
            $passed = $percentage >= $quiz->passing_score;
            
            // Update attempt
            $attempt->score = $score;
            $attempt->total_points = $totalPoints;
            $attempt->percentage = $percentage;
            $attempt->passed = $passed ? 1 : 0;
            $attempt->completed_at = now();
            $attempt->answers = $answers;
            
            if ($attempt->started_at) {
                $attempt->time_taken = now()->diffInSeconds($attempt->started_at);
            }
            
            $attempt->save();
            
            DB::commit();
            
            // Prepare results for modal
            $results = [
                'score' => $score,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'passing_score' => $quiz->passing_score,
                'questions' => []
            ];
            
            // Store detailed question results
            foreach ($quiz->questions as $question) {
                $userAnswer = $answers[$question->id] ?? null;
                $correctOption = $question->options->where('is_correct', 1)->first();
                $isCorrect = false;
                
                if ($userAnswer && $correctOption) {
                    $isCorrect = ($userAnswer == $correctOption->id);
                }
                
                // Get all options with status
                $optionsData = [];
                foreach ($question->options as $option) {
                    $optionsData[] = [
                        'id' => $option->id,
                        'text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                        'is_user_selected' => ($option->id == $userAnswer)
                    ];
                }
                
                $results['questions'][] = [
                    'question' => $question->question,
                    'question_id' => $question->id,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $correctOption ? $correctOption->id : null,
                    'correct_text' => $correctOption ? $correctOption->option_text : null,
                    'is_correct' => $isCorrect,
                    'options' => $optionsData
                ];
            }
            
            // Store results in session for modal
            session()->flash('quiz_results', $results);
            
            return redirect()->route('student.quizzes.show', $encryptedId)
                ->with('success', 'Quiz submitted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting quiz: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error submitting quiz: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Clear quiz attempt and start over - UNLIMITED ATTEMPTS
     */
    public function retake($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $userId = Auth::id();
            
            $quiz = Quiz::findOrFail($quizId);
            
            // UNLIMITED ATTEMPTS - No max attempts check
            
            // Delete any incomplete attempts
            QuizAttempt::where('quiz_id', $quizId)
                ->where('user_id', $userId)
                ->whereNull('completed_at')
                ->delete();
            
            return redirect()->route('student.quizzes.show', $encryptedId);
            
        } catch (\Exception $e) {
            \Log::error('Error retaking quiz: ' . $e->getMessage());
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Error retaking quiz: ' . $e->getMessage());
        }
    }
}