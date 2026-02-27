<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Traits\CacheManager;

class QuizController extends Controller
{
    use CacheManager;
    
    // REMOVED index() method - redirects to todo
    
    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        // Debug form submission
        \Log::info('========== FORM SUBMISSION DEBUG ==========');
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
            // Use the checkbox value - if not present, default to 0 (draft)
            'is_published' => $request->has('is_published') ? 1 : 0,
            'created_by' => auth()->id(), // <-- ADD THIS LINE
            'duration' => 60,
            'total_questions' => 0,
            'passing_score' => 70,
            'available_from' => null,
            'available_until' => null,
        ];

        $quiz = Quiz::create($quizData);
        
        // Log to confirm creator is set
        \Log::info('Quiz created:', [
            'quiz_id' => $quiz->id,
            'title' => $quiz->title,
            'created_by' => $quiz->created_by,
            'creator_name' => auth()->user()->f_name . ' ' . auth()->user()->l_name
        ]);

        // Rest of your code remains the same...
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

        // Clear ALL quiz caches across all roles
        $this->clearAllQuizCaches();
        
        \Log::info('New quiz created - ID: ' . $quiz->id . ', Title: ' . $quiz->title);
        \Log::info('Published status: ' . ($quiz->is_published ? 'Published' : 'Draft'));

        // Redirect to To-Do with quiz filter
        return redirect()->route('admin.todo.index', ['type' => 'quiz'])
            ->with('success', 'Quiz created successfully.');
    }

    public function show($encryptedId)
    {
        // Redirect to the unified todo quiz show page
        return redirect()->route('admin.todo.quiz.show', $encryptedId);
    }
    
    public function edit($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            
            // Keep caching for edit page
            $cacheKey = 'admin_quiz_edit_' . $id;
            
            $quiz = Cache::remember($cacheKey, 300, function() use ($id) {
                return Quiz::with(['questions.options' => function($query) {
                        $query->orderBy('order');
                    }])
                    ->findOrFail($id);
            });
            
            return view('admin.quizzes.edit', compact('quiz'));
            
        } catch (\Exception $e) {
            \Log::error('Error editing quiz', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.todo.index', ['type' => 'quiz'])
                ->with('error', 'Quiz not found or invalid link.');
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $quizId = Crypt::decrypt($id);
            $quiz = Quiz::findOrFail($quizId);
            
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
            
            // Update quiz basic info - INCLUDING is_published
            $quiz->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_published' => $request->has('is_published') ? 1 : 0, // THIS WAS MISSING
                'duration' => $request->duration ?? $quiz->duration,
                'total_questions' => $request->total_questions ?? $quiz->total_questions,
                'passing_score' => $request->passing_score ?? $quiz->passing_score,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
            ]);
            
            // Process questions (rest of your code remains the same)
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
            
            // Clear ALL quiz caches
            $this->clearAllQuizCaches();
            Cache::forget('admin_quiz_show_' . $quiz->id);
            Cache::forget('admin_quiz_edit_' . $quiz->id);
            Cache::forget('admin_quiz_take_' . $quiz->id);
            
            return redirect()->route('admin.quizzes.show', Crypt::encrypt($quiz->id))
                ->with('success', 'Quiz updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating quiz', [
                'quiz_id' => $quizId ?? null,
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

    private function updateQuestionOptions(QuizQuestion $question, array $options, $correctAnswerIndex)
    {
        // First, get all existing option IDs
        $existingOptionIds = $question->options->pluck('id')->toArray();
        $submittedOptionIds = [];
        $optionOrder = 1;
        
        // Process each option
        foreach ($options as $optionIndex => $optionData) {
            // Skip if option text is empty
            if (empty($optionData['option_text'])) {
                continue;
            }
            
            if (isset($optionData['id']) && !empty($optionData['id'])) {
                // Update existing option
                $option = QuizOption::where('id', $optionData['id'])
                    ->where('quiz_question_id', $question->id)
                    ->first();
                    
                if ($option) {
                    $option->update([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => ($correctAnswerIndex == $optionIndex) ? 1 : 0,
                        'order' => $optionOrder++,
                    ]);
                    $submittedOptionIds[] = $option->id;
                }
            } else {
                // Create new option
                $option = QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text' => $optionData['option_text'],
                    'is_correct' => ($correctAnswerIndex == $optionIndex) ? 1 : 0,
                    'order' => $optionOrder++,
                ]);
                $submittedOptionIds[] = $option->id;
            }
        }
        
        // Delete options that were removed from the form
        $optionsToDelete = array_diff($existingOptionIds, $submittedOptionIds);
        if (!empty($optionsToDelete)) {
            QuizOption::whereIn('id', $optionsToDelete)->delete();
        }
    }

    private function createQuestionOptions(QuizQuestion $question, array $options, $correctAnswerIndex)
    {
        $optionOrder = 1;
        
        foreach ($options as $optionIndex => $optionData) {
            // Skip if option text is empty
            if (empty($optionData['option_text'])) {
                continue;
            }
            
            QuizOption::create([
                'quiz_question_id' => $question->id,
                'option_text' => $optionData['option_text'],
                'is_correct' => ($correctAnswerIndex == $optionIndex) ? 1 : 0,
                'order' => $optionOrder++,
            ]);
        }
    }

    public function destroy($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $quiz = Quiz::findOrFail($id);
            
            // Delete quiz (cascade will delete questions and options)
            $quiz->delete();
            
            // Clear ALL quiz caches
            $this->clearAllQuizCaches();
            Cache::forget('admin_quiz_show_' . $id);
            Cache::forget('admin_quiz_edit_' . $id);
            Cache::forget('admin_quiz_take_' . $id);

            return redirect()->route('admin.todo.index', ['type' => 'quiz'])
                ->with('success', 'Quiz deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting quiz', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.todo.index', ['type' => 'quiz'])
                ->with('error', 'Quiz not found or invalid link.');
        }
    }

    /**
     * Show the quiz taking page (preview mode for admin)
     */
    public function take($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            
            $cacheKey = 'admin_quiz_take_' . $quizId;
            
            $quiz = Cache::remember($cacheKey, 300, function() use ($quizId) {
                return Quiz::with(['questions.options' => function($query) {
                        $query->select(['id', 'quiz_question_id', 'option_text', 'order']);
                    }])
                    ->select(['id', 'title', 'description', 'duration', 'total_questions', 'passing_score'])
                    ->findOrFail($quizId);
            });
            
            return view('admin.quizzes.take', compact('quiz'));
            
        } catch (\Exception $e) {
            \Log::error('Error taking quiz', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.todo.index', ['type' => 'quiz'])
                ->with('error', 'Invalid quiz or quiz not found.');
        }
    }
    
    public function submit(Request $request, $encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            
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
            Cache::forget('admin_quiz_take_' . $quizId);
            
            // Store results in session and redirect back to show page
            return redirect()->route('admin.quizzes.show', Crypt::encrypt($quiz->id))
                ->with('results', $results)
                ->with('score', $score)
                ->with('totalPoints', $totalPoints)
                ->with('percentage', $percentage)
                ->with('passed', $passed)
                ->with('success', 'Quiz submitted successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Error submitting quiz', [
                'encryptedId' => $encryptedId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.todo.index', ['type' => 'quiz'])
                ->with('error', 'Error submitting quiz: ' . $e->getMessage());
        }
    }
    
    /**
     * Show quiz results
     */
    public function results($encryptedId)
    {
        return redirect()->route('admin.quizzes.take', $encryptedId);
    }

    /**
     * Clear all quiz-related caches
     */
    private function clearQuizCaches()
    {
        $this->clearAllQuizCaches();
    }

    /**
     * Manual cache clearing endpoint
     */
    public function clearCache()
    {
        $this->clearAllQuizCaches();
        
        return redirect()->route('admin.todo.index', ['type' => 'quiz'])
            ->with('success', 'All quiz caches cleared successfully across all roles!');
    }

    /**
     * Publish quiz
     */
    public function publish($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $quiz = Quiz::findOrFail($id);
            
            $quiz->update([
                'is_published' => true
            ]);
            
            // Clear caches
            $this->clearAllQuizCaches();
            Cache::forget('admin_quiz_show_' . $id);
            Cache::forget('admin_quiz_edit_' . $id);
            
            return redirect()->route('admin.quizzes.show', $encryptedId)
                ->with('success', 'Quiz published successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Error publishing quiz: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error publishing quiz: ' . $e->getMessage());
        }
    }

    /**
     * Toggle quiz publish status
     */
    public function togglePublish(Request $request, $encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
            $quiz = Quiz::findOrFail($id);
            
            // Toggle the publish status
            $quiz->update([
                'is_published' => !$quiz->is_published
            ]);
            
            $status = $quiz->is_published ? 'published' : 'unpublished';
            
            return redirect()->back()->with('success', "Quiz {$status} successfully.");
            
        } catch (\Exception $e) {
            \Log::error('Error toggling quiz publish status: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update quiz status.');
        }
    }
}