<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::latest()->paginate(10);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        // Only validate title and description - remove other validations
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // REMOVE all other validations that are not in your form
        ]);

        // Set default values for all other fields
        $quizData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'is_published' => 1, // Automatically published
            'duration' => 60, // Default 60 minutes
            'total_questions' => count($request->questions ?? []), // Count actual questions
            'passing_score' => 70, // Default 70%
            'available_from' => null,
            'available_until' => null,
        ];

        $quiz = Quiz::create($quizData);

        // Save questions if provided
        if ($request->has('questions')) {
            $questionOrder = 1;
            
            foreach ($request->questions as $questionData) {
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $questionData['question'],
                    'type' => $questionData['type'],
                    'points' => 1, // Default 1 point
                    'order' => $questionOrder++,
                    'explanation' => $questionData['explanation'] ?? null,
                ]);

                // Save options
                if (isset($questionData['options'])) {
                    $optionOrder = 1;
                    $correctAnswerIndex = $questionData['correct_answer'] ?? null;
                    
                    foreach ($questionData['options'] as $optionIndex => $optionData) {
                        // Determine if this option is correct
                        $isCorrect = false;
                        
                        if ($question->type === 'single') {
                            // For single choice: check if this option index matches the correct_answer
                            $isCorrect = ($optionIndex == $correctAnswerIndex);
                        } else {
                            // For multiple choice: check if is_correct is set to '1'
                            $isCorrect = isset($optionData['is_correct']) && $optionData['is_correct'] == '1';
                        }
                        
                        QuizOption::create([
                            'quiz_question_id' => $question->id,
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $isCorrect ? 1 : 0,
                            'order' => $optionOrder++,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        
        // If there are results in session, pass them to view
        if (session('results')) {
            return view('admin.quizzes.show', compact('quiz'))
                ->with('results', session('results'))
                ->with('score', session('score'))
                ->with('totalPoints', session('totalPoints'))
                ->with('percentage', session('percentage'))
                ->with('passed', session('passed'));
        }
        
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'is_published' => 'boolean',
            'duration' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
        ]);

        $quiz->update($validated);

        // Update or create questions
        if ($request->has('questions')) {
            $existingQuestionIds = [];
            
            foreach ($request->questions as $questionData) {
                if (isset($questionData['id'])) {
                    // Update existing question
                    $question = QuizQuestion::where('id', $questionData['id'])
                        ->where('quiz_id', $quiz->id)
                        ->first();
                    
                    if ($question) {
                        $question->update([
                            'question' => $questionData['question'],
                            'type' => $questionData['type'],
                            'points' => $questionData['points'] ?? 1,
                            'explanation' => $questionData['explanation'] ?? null,
                        ]);
                        
                        $existingQuestionIds[] = $question->id;
                    }
                } else {
                    // Create new question
                    $question = QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question' => $questionData['question'],
                        'type' => $questionData['type'],
                        'points' => $questionData['points'] ?? 1,
                        'order' => $questionData['order'] ?? 1,
                        'explanation' => $questionData['explanation'] ?? null,
                    ]);
                    
                    $existingQuestionIds[] = $question->id;
                }

                // Update or create options
                if (isset($questionData['options'])) {
                    $existingOptionIds = [];
                    
                    foreach ($questionData['options'] as $optionData) {
                        if (isset($optionData['id'])) {
                            // Update existing option
                            $option = QuizOption::where('id', $optionData['id'])
                                ->where('quiz_question_id', $question->id)
                                ->first();
                            
                            if ($option) {
                                $option->update([
                                    'option_text' => $optionData['option_text'],
                                    'is_correct' => isset($optionData['is_correct']) && $optionData['is_correct'] == '1',
                                ]);
                                
                                $existingOptionIds[] = $option->id;
                            }
                        } else {
                            // Create new option
                            $option = QuizOption::create([
                                'quiz_question_id' => $question->id,
                                'option_text' => $optionData['option_text'],
                                'is_correct' => isset($optionData['is_correct']) && $optionData['is_correct'] == '1',
                                'order' => $optionData['order'] ?? 1,
                            ]);
                            
                            $existingOptionIds[] = $option->id;
                        }
                    }

                    // Delete options not in the request
                    if (!empty($existingOptionIds)) {
                        QuizOption::where('quiz_question_id', $question->id)
                            ->whereNotIn('id', $existingOptionIds)
                            ->delete();
                    }
                }
            }

            // Delete questions not in the request
            if (!empty($existingQuestionIds)) {
                QuizQuestion::where('quiz_id', $quiz->id)
                    ->whereNotIn('id', $existingQuestionIds)
                    ->delete();
            }
        }

        return redirect()->route('admin.quizzes.show', Crypt::encrypt($quiz->id))
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    /**
     * Show the quiz taking page (preview mode for admin)
     */
    public function take($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $quiz = Quiz::with(['questions.options'])->findOrFail($quizId);
            
            return view('admin.quizzes.take', compact('quiz'));
        } catch (\Exception $e) {
            return redirect()->route('admin.quizzes.index')
                ->with('error', 'Invalid quiz or quiz not found.');
        }
    }
    
    public function submit(Request $request, $encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            $quiz = Quiz::with(['questions.options'])->findOrFail($quizId);
            
            $results = [];
            $score = 0;
            $totalPoints = 0;
            
            foreach ($quiz->questions as $question) {
                $totalPoints += $question->points;
                $userAnswer = $request->input("question_{$question->id}", []);
                $userAnswer = is_array($userAnswer) ? $userAnswer : [$userAnswer];
                
                $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();
                $isCorrect = false;
                
                if ($question->type === 'single') {
                    $isCorrect = !empty($userAnswer) && in_array($userAnswer[0], $correctOptions);
                } else {
                    $isCorrect = empty(array_diff($correctOptions, $userAnswer)) && 
                                empty(array_diff($userAnswer, $correctOptions));
                }
                
                $points = $isCorrect ? $question->points : 0;
                $score += $points;
                
                $results[] = [
                    'question' => $question,
                    'user_answer' => $userAnswer,
                    'correct_options' => $correctOptions,
                    'is_correct' => $isCorrect,
                    'points' => $points
                ];
            }
            
            $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;
            $passed = $percentage >= $quiz->passing_score;
            
            // Store results in session and redirect back to show page
            return redirect()->route('admin.quizzes.show', Crypt::encrypt($quiz->id))
                ->with('results', $results)
                ->with('score', $score)
                ->with('totalPoints', $totalPoints)
                ->with('percentage', $percentage)
                ->with('passed', $passed)
                ->with('success', 'Quiz submitted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.quizzes.index')
                ->with('error', 'Error submitting quiz: ' . $e->getMessage());
        }
    }
    
    /**
     * Show quiz results
     */
    public function results($encryptedId)
    {
        // This method might be called to show results separately
        // You can redirect to the take method or show results differently
        return redirect()->route('admin.quizzes.take', $encryptedId);
    }
}