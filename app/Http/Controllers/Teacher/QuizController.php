<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\CacheManager;

class QuizController extends Controller
{
    use CacheManager;
    
    public function index()
    {
        $teacherId = Auth::id();
        
        // 🔥 FIX: REMOVE CACHING - Get fresh data every time
        $quizzes = Quiz::select(['id', 'title', 'description', 'is_published', 'duration', 'total_questions', 'passing_score', 'created_at', 'updated_at'])
            ->withCount('questions')
            ->latest()
            ->paginate(10);
        
        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('teacher.quizzes.create');
    }

    public function store(Request $request)
    {
        // Debug form submission
        \Log::info('========== TEACHER QUIZ FORM SUBMISSION DEBUG ==========');
        \Log::info('Request method: ' . $request->method());
        \Log::info('All request data:', $request->all());

        // Validate basic quiz fields
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Set default values for all other fields
        $quizData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'is_published' => 1,
            'duration' => 60,
            'total_questions' => 0,
            'passing_score' => 70,
            'available_from' => null,
            'available_until' => null,
            'created_by' => Auth::id(),
        ];

        $quiz = Quiz::create($quizData);

        // Save questions if provided
        $validQuestionCount = 0;
        if ($request->has('questions')) {
            $questionOrder = 1;
            
            foreach ($request->questions as $questionData) {
                // Skip if question text is empty
                if (empty($questionData['question'])) {
                    continue;
                }
                
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $questionData['question'],
                    'points' => 1,
                    'order' => $questionOrder++,
                    'explanation' => $questionData['explanation'] ?? null,
                ]);

                // Save options
                if (isset($questionData['options'])) {
                    $optionOrder = 1;
                    $correctAnswerIndex = $questionData['correct_answer'] ?? null;
                    
                    foreach ($questionData['options'] as $optionIndex => $optionData) {
                        // Skip if option text is empty
                        if (empty($optionData['option_text'])) {
                            continue;
                        }
                        
                        // Determine if this option is correct
                        $isCorrect = ($optionIndex == $correctAnswerIndex);
                        
                        QuizOption::create([
                            'quiz_question_id' => $question->id,
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $isCorrect ? 1 : 0,
                            'order' => $optionOrder++,
                        ]);
                    }
                }
                
                $validQuestionCount++;
            }
        }

        // Update the quiz with the actual number of questions
        $quiz->update([
            'total_questions' => $validQuestionCount
        ]);

        // 🔥 FIX: Clear ALL quiz caches across all roles
        $this->clearAllQuizCaches(Auth::id());
        
        \Log::info('New quiz created by teacher - ID: ' . $quiz->id . ', Title: ' . $quiz->title);

        return redirect()->route('teacher.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function show($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $cacheKey = 'teacher_quiz_show_' . $id . '_teacher_' . $teacherId;
            
            $quiz = Cache::remember($cacheKey, 600, function() use ($id) {
                return Quiz::with(['questions.options' => function($query) {
                        $query->select(['id', 'quiz_question_id', 'option_text', 'is_correct', 'order']);
                    }])
                    ->select(['id', 'title', 'description', 'is_published', 'duration', 'total_questions', 'passing_score', 'available_from', 'available_until', 'created_at', 'updated_at'])
                    ->withCount('questions')
                    ->findOrFail($id);
            });
            
            // If there are results in session, pass them to view
            if (session('results')) {
                return view('teacher.quizzes.show', compact('quiz'))
                    ->with('results', session('results'))
                    ->with('score', session('score'))
                    ->with('totalPoints', session('totalPoints'))
                    ->with('percentage', session('percentage'))
                    ->with('passed', session('passed'));
            }
            
            return view('teacher.quizzes.show', compact('quiz'));
            
        } catch (\Exception $e) {
            \Log::error('Error showing teacher quiz', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.quizzes.index')
                ->with('error', 'Quiz not found or invalid link.');
        }
    }

    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $cacheKey = 'teacher_quiz_edit_' . $id . '_teacher_' . $teacherId;
            
            $quiz = Cache::remember($cacheKey, 300, function() use ($id) {
                return Quiz::with(['questions.options' => function($query) {
                        $query->orderBy('order');
                    }])
                    ->findOrFail($id);
            });
            
            return view('teacher.quizzes.edit', compact('quiz'));
            
        } catch (\Exception $e) {
            \Log::error('Error editing teacher quiz', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.quizzes.index')
                ->with('error', 'Quiz not found or invalid link.');
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $quizId = Crypt::decrypt($id);
            $quiz = Quiz::findOrFail($quizId);
            $teacherId = Auth::id();
            
            // Validate basic quiz info
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'duration' => 'nullable|integer|min:1',
                'total_questions' => 'nullable|integer|min:1',
                'passing_score' => 'nullable|integer|min:1|max:100',
                'available_from' => 'nullable|date',
                'available_until' => 'nullable|date|after:available_from',
            ]);
            
            // Update quiz basic info
            $quiz->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration ?? $quiz->duration,
                'total_questions' => $request->total_questions ?? $quiz->total_questions,
                'passing_score' => $request->passing_score ?? $quiz->passing_score,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
            ]);
            
            // Process questions
            if ($request->has('questions')) {
                $processedQuestionIds = [];
                
                foreach ($request->questions as $questionIndex => $questionData) {
                    // Skip if question text is empty
                    if (empty($questionData['question'])) {
                        continue;
                    }
                    
                    // Check if this is an existing question
                    if (!empty($questionData['id'])) {
                        // Update existing question
                        $question = QuizQuestion::where('id', $questionData['id'])
                            ->where('quiz_id', $quiz->id)
                            ->first();
                        
                        if ($question) {
                            $question->update([
                                'question' => $questionData['question'],
                                'explanation' => $questionData['explanation'] ?? null,
                                'order' => $questionIndex + 1,
                            ]);
                            
                            // Process options for this question
                            $this->processQuestionOptions($question, $questionData);
                            $processedQuestionIds[] = $question->id;
                        }
                    } else {
                        // Create new question
                        $question = QuizQuestion::create([
                            'quiz_id' => $quiz->id,
                            'question' => $questionData['question'],
                            'points' => 1,
                            'order' => $questionIndex + 1,
                            'explanation' => $questionData['explanation'] ?? null,
                        ]);
                        
                        // Process options for new question
                        $this->processQuestionOptions($question, $questionData);
                        $processedQuestionIds[] = $question->id;
                    }
                }
                
                // Delete questions that were removed from the form
                $existingQuestionIds = $quiz->questions->pluck('id')->toArray();
                $questionsToDelete = array_diff($existingQuestionIds, $processedQuestionIds);
                
                if (!empty($questionsToDelete)) {
                    // Delete options first
                    QuizOption::whereIn('quiz_question_id', $questionsToDelete)->delete();
                    // Then delete questions
                    QuizQuestion::whereIn('id', $questionsToDelete)->delete();
                }
                
                // Update total questions count
                $quiz->update([
                    'total_questions' => QuizQuestion::where('quiz_id', $quiz->id)->count()
                ]);
            }
            
            DB::commit();
            
            // 🔥 FIX: Clear ALL quiz caches
            $this->clearAllQuizCaches($teacherId);
            Cache::forget('teacher_quiz_show_' . $quiz->id . '_teacher_' . $teacherId);
            Cache::forget('teacher_quiz_edit_' . $quiz->id . '_teacher_' . $teacherId);
            Cache::forget('teacher_quiz_take_' . $quiz->id . '_teacher_' . $teacherId);
            
            return redirect()->route('teacher.quizzes.show', Crypt::encrypt($quiz->id))
                ->with('success', 'Quiz updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating teacher quiz', [
                'quiz_id' => $quizId ?? null,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error updating quiz: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function processQuestionOptions(QuizQuestion $question, array $questionData)
    {
        // Get all existing option IDs for this question
        $existingOptionIds = $question->options->pluck('id')->toArray();
        $processedOptionIds = [];
        
        // Process each submitted option
        if (isset($questionData['options']) && is_array($questionData['options'])) {
            $optionOrder = 1;
            
            foreach ($questionData['options'] as $optionIndex => $optionData) {
                // Skip if option text is empty
                if (empty($optionData['option_text'])) {
                    continue;
                }
                
                // Determine if this is the correct answer
                $isCorrect = isset($questionData['correct_answer']) && 
                            $questionData['correct_answer'] == $optionIndex;
                
                // Check if this is an existing option
                if (!empty($optionData['id'])) {
                    // Update existing option
                    $option = QuizOption::where('id', $optionData['id'])
                        ->where('quiz_question_id', $question->id)
                        ->first();
                        
                    if ($option) {
                        $option->update([
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $isCorrect ? 1 : 0,
                            'order' => $optionOrder++,
                        ]);
                        $processedOptionIds[] = $option->id;
                    }
                } else {
                    // Create new option
                    $option = QuizOption::create([
                        'quiz_question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $isCorrect ? 1 : 0,
                        'order' => $optionOrder++,
                    ]);
                    $processedOptionIds[] = $option->id;
                }
            }
        }
        
        // Delete options that were removed from the form
        $optionsToDelete = array_diff($existingOptionIds, $processedOptionIds);
        if (!empty($optionsToDelete)) {
            QuizOption::whereIn('id', $optionsToDelete)->delete();
        }
    }

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $quiz = Quiz::findOrFail($id);
            $teacherId = Auth::id();
            
            $quiz->delete();
            
            // 🔥 FIX: Clear ALL quiz caches
            $this->clearAllQuizCaches($teacherId);
            Cache::forget('teacher_quiz_show_' . $id . '_teacher_' . $teacherId);
            Cache::forget('teacher_quiz_edit_' . $id . '_teacher_' . $teacherId);
            Cache::forget('teacher_quiz_take_' . $id . '_teacher_' . $teacherId);

            return redirect()->route('teacher.quizzes.index')
                ->with('success', 'Quiz deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting teacher quiz', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.quizzes.index')
                ->with('error', 'Quiz not found or invalid link.');
        }
    }

    /**
     * Show the quiz taking page (preview mode for teacher)
     */
    public function take($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            $cacheKey = 'teacher_quiz_take_' . $quizId . '_teacher_' . $teacherId;
            
            $quiz = Cache::remember($cacheKey, 300, function() use ($quizId) {
                return Quiz::with(['questions.options' => function($query) {
                        $query->select(['id', 'quiz_question_id', 'option_text', 'order']);
                    }])
                    ->select(['id', 'title', 'description', 'duration', 'total_questions', 'passing_score'])
                    ->findOrFail($quizId);
            });
            
            return view('teacher.quizzes.take', compact('quiz'));
            
        } catch (\Exception $e) {
            \Log::error('Error taking teacher quiz', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.quizzes.index')
                ->with('error', 'Invalid quiz or quiz not found.');
        }
    }
    
    public function submit(Request $request, $encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $teacherId = Auth::id();
            
            // Don't cache this - we need fresh data
            $quiz = Quiz::with(['questions.options' => function($query) {
                    $query->where('is_correct', true);
                }])
                ->findOrFail($quizId);
            
            $results = [];
            $score = 0;
            $totalPoints = 0;
            
            foreach ($quiz->questions as $question) {
                $totalPoints += 1;
                $userAnswer = $request->input("question_{$question->id}");
                
                $correctOption = $question->options->where('is_correct', true)->first();
                $isCorrect = false;
                
                if ($correctOption && $userAnswer == $correctOption->id) {
                    $isCorrect = true;
                    $score += 1;
                }
                
                $results[] = [
                    'question' => $question,
                    'user_answer' => $userAnswer,
                    'correct_option' => $correctOption ? $correctOption->id : null,
                    'is_correct' => $isCorrect,
                    'points' => $isCorrect ? 1 : 0
                ];
            }
            
            $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;
            $passed = $percentage >= $quiz->passing_score;
            
            // Clear quiz take cache after submission
            Cache::forget('teacher_quiz_take_' . $quizId . '_teacher_' . $teacherId);
            
            // Store results in session and redirect back to show page
            return redirect()->route('teacher.quizzes.show', Crypt::encrypt($quiz->id))
                ->with('results', $results)
                ->with('score', $score)
                ->with('totalPoints', $totalPoints)
                ->with('percentage', $percentage)
                ->with('passed', $passed)
                ->with('success', 'Quiz submitted successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Error submitting teacher quiz', [
                'encryptedId' => $encryptedId,
                'teacher_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('teacher.quizzes.index')
                ->with('error', 'Error submitting quiz: ' . $e->getMessage());
        }
    }
    
    /**
     * Show quiz results
     */
    public function results($encryptedId)
    {
        return redirect()->route('teacher.quizzes.take', $encryptedId);
    }

    /**
     * Clear all quiz-related caches
     */
    private function clearQuizCaches($teacherId)
    {
        // Call the comprehensive cache clearing method from CacheManager
        $this->clearAllQuizCaches($teacherId);
    }

    /**
     * Manual cache clearing endpoint
     */
    public function clearCache()
    {
        $teacherId = Auth::id();
        $this->clearAllQuizCaches($teacherId);
        
        return redirect()->route('teacher.quizzes.index')
            ->with('success', 'All quiz caches cleared successfully!');
    }
}