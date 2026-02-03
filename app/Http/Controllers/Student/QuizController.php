<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizController extends Controller
{
    /**
     * Display available quizzes
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get enrolled course IDs
        $enrolledCourseIds = $user->enrollments()
            ->pluck('course_id')
            ->toArray();
        
        // Get available quizzes (for enrolled courses, not expired, published)
        $availableQuizzes = Quiz::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', true)
            ->where(function($query) {
                $query->whereNull('available_until')
                      ->orWhere('available_until', '>', now());
            })
            ->where(function($query) {
                $query->whereNull('available_from')
                      ->orWhere('available_from', '<=', now());
            })
            ->with(['course', 'questions'])
            ->orderBy('available_from', 'desc')
            ->get();
        
        // Get completed quizzes
        $completedQuizzes = QuizAttempt::where('user_id', $user->id)
            ->with(['quiz.course'])
            ->orderBy('completed_at', 'desc')
            ->get();
        
        // Separate quizzes by status
        $upcomingQuizzes = $availableQuizzes->filter(function($quiz) use ($user) {
            return !$user->quizAttempts()->where('quiz_id', $quiz->id)->exists();
        });
        
        return view('student.quizzes.index', compact(
            'upcomingQuizzes',
            'completedQuizzes'
        ));
    }

    /**
     * Show a specific quiz
     */
    public function show($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $user = Auth::user();
            
            $quiz = Quiz::with(['course', 'questions.options'])
                ->where('is_published', true)
                ->findOrFail($quizId);
            
            // Check if student is enrolled in the course
            $isEnrolled = $user->enrollments()
                ->where('course_id', $quiz->course_id)
                ->exists();
            
            if (!$isEnrolled) {
                return redirect()->route('student.quizzes.index')
                    ->with('error', 'You need to be enrolled in the course to access this quiz.');
            }
            
            // Check if quiz is available
            if ($quiz->available_from && $quiz->available_from > now()) {
                return redirect()->route('student.quizzes.index')
                    ->with('error', 'This quiz is not available yet. Available from: ' . $quiz->available_from->format('M d, Y H:i'));
            }
            
            if ($quiz->available_until && $quiz->available_until < now()) {
                return redirect()->route('student.quizzes.index')
                    ->with('error', 'This quiz has expired.');
            }
            
            // Check previous attempts
            $previousAttempts = $user->quizAttempts()
                ->where('quiz_id', $quizId)
                ->orderBy('completed_at', 'desc')
                ->get();
            
            $canTakeQuiz = $previousAttempts->isEmpty();
            
            return view('student.quizzes.show', compact(
                'quiz',
                'previousAttempts',
                'canTakeQuiz'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Quiz not found.');
        }
    }

    /**
     * Start taking a quiz
     */
    public function take($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $user = Auth::user();
            
            $quiz = Quiz::with(['questions.options' => function($query) {
                $query->orderBy('order');
            }])->where('is_published', true)
               ->findOrFail($quizId);
            
            // Check enrollment
            $isEnrolled = $user->enrollments()
                ->where('course_id', $quiz->course_id)
                ->exists();
            
            if (!$isEnrolled) {
                return redirect()->route('student.quizzes.index')
                    ->with('error', 'You need to be enrolled in the course to take this quiz.');
            }
            
            // Check if quiz is available
            if ($quiz->available_from && $quiz->available_from > now()) {
                return redirect()->route('student.quizzes.show', $encryptedId)
                    ->with('error', 'Quiz is not available yet.');
            }
            
            if ($quiz->available_until && $quiz->available_until < now()) {
                return redirect()->route('student.quizzes.show', $encryptedId)
                    ->with('error', 'Quiz has expired.');
            }
            
            // Check if already attempted
            $previousAttempt = $user->quizAttempts()
                ->where('quiz_id', $quizId)
                ->first();
            
            if ($previousAttempt) {
                return redirect()->route('student.quizzes.results', $encryptedId)
                    ->with('info', 'You have already taken this quiz.');
            }
            
            // Start timing
            session()->put('quiz_start_time', now());
            session()->put('quiz_id', $quizId);
            
            return view('student.quizzes.take', compact('quiz'));
            
        } catch (\Exception $e) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Quiz not found.');
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
            $user = Auth::user();
            
            $quiz = Quiz::with(['questions.options'])->findOrFail($quizId);
            
            // Validate quiz timing
            $quizStartTime = session()->get('quiz_start_time');
            if (!$quizStartTime) {
                return redirect()->route('student.quizzes.take', $encryptedId)
                    ->with('error', 'Quiz session expired. Please start again.');
            }
            
            $timeTaken = now()->diffInMinutes($quizStartTime);
            
            // Check if time limit exceeded
            if ($quiz->duration && $timeTaken > $quiz->duration) {
                return redirect()->route('student.quizzes.take', $encryptedId)
                    ->with('error', 'Time limit exceeded. Please submit within ' . $quiz->duration . ' minutes.');
            }
            
            // Calculate score
            $score = 0;
            $totalQuestions = $quiz->questions->count();
            $results = [];
            
            foreach ($quiz->questions as $question) {
                $userAnswer = $request->input("question_{$question->id}");
                $correctOption = $question->options->where('is_correct', true)->first();
                
                $isCorrect = false;
                if ($correctOption && $userAnswer == $correctOption->id) {
                    $isCorrect = true;
                    $score += $question->points; // Use question points
                }
                
                $results[] = [
                    'question' => $question,
                    'user_answer' => $userAnswer,
                    'correct_option' => $correctOption,
                    'is_correct' => $isCorrect,
                ];
            }
            
            $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0;
            $passed = $percentage >= $quiz->passing_score;
            
            // Create quiz attempt record
            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quizId,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'percentage' => $percentage,
                'passed' => $passed,
                'time_taken' => $timeTaken,
                'completed_at' => now(),
                'answers' => json_encode($results),
            ]);
            
            // Clear session
            session()->forget(['quiz_start_time', 'quiz_id']);
            
            DB::commit();
            
            return redirect()->route('student.quizzes.results', $encryptedId)
                ->with('success', 'Quiz submitted successfully!')
                ->with('attempt_id', $attempt->id);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('student.quizzes.take', $encryptedId)
                ->with('error', 'Error submitting quiz: ' . $e->getMessage());
        }
    }

    /**
     * Show quiz results
     */
    public function results($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $user = Auth::user();
            
            $quiz = Quiz::with(['course'])->findOrFail($quizId);
            
            // Get latest attempt
            $attempt = $user->quizAttempts()
                ->where('quiz_id', $quizId)
                ->latest()
                ->first();
            
            if (!$attempt) {
                return redirect()->route('student.quizzes.show', $encryptedId)
                    ->with('error', 'No quiz attempt found.');
            }
            
            // Decode answers if stored as JSON
            $results = [];
            if ($attempt->answers) {
                $results = json_decode($attempt->answers, true);
            } else {
                // Recalculate if not stored
                $results = $this->recalculateResults($quiz, $attempt);
            }
            
            return view('student.quizzes.results', compact(
                'quiz',
                'attempt',
                'results'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Results not found.');
        }
    }

    /**
     * Show all quiz attempts
     */
    public function attempts()
    {
        $user = Auth::user();
        
        $attempts = $user->quizAttempts()
            ->with(['quiz.course'])
            ->orderBy('completed_at', 'desc')
            ->paginate(10);
        
        // Calculate statistics
        $totalAttempts = $attempts->total();
        $passedAttempts = $user->quizAttempts()->where('passed', true)->count();
        $averageScore = $user->quizAttempts()->avg('percentage') ?? 0;
        $bestScore = $user->quizAttempts()->max('percentage') ?? 0;
        
        return view('student.quizzes.attempts', compact(
            'attempts',
            'totalAttempts',
            'passedAttempts',
            'averageScore',
            'bestScore'
        ));
    }

    /**
     * Helper method to recalculate results
     */
    private function recalculateResults($quiz, $attempt)
    {
        $results = [];
        
        // This would need to be implemented based on how you store answers
        // For now, return empty array
        return $results;
    }

    /**
     * Get quiz instructions
     */
    public function instructions($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            
            $quiz = Quiz::with(['course'])->findOrFail($quizId);
            
            return view('student.quizzes.instructions', compact('quiz'));
            
        } catch (\Exception $e) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Quiz not found.');
        }
    }
}