<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        // Remove the user filtering since we don't have created_by column
        // For now, show all quizzes. Later you'll need to add a user_id column
        $quizzes = Quiz::latest()->paginate(10);
        
        // OR if you want to temporarily store user info in another way:
        // Option 1: Add user_id to your quizzes table (recommended)
        // Option 2: Use a separate table to track quiz ownership
        
        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        // Get courses taught by this teacher
        $courses = Course::where('teacher_id', Auth::id())->get();
        return view('teacher.quizzes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        // Only validate title and description - remove other validations
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        // Set default values for all other fields
        // REMOVED: 'created_by' => Auth::id(), and 'user_type' => 'teacher',
        $quizData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'course_id' => $validated['course_id'] ?? null,
            'is_published' => 0, // Teachers might need admin approval or can self-publish
            'duration' => 60, // Default 60 minutes
            'total_questions' => count($request->questions ?? []), // Count actual questions
            'passing_score' => 70, // Default 70%
            'available_from' => null,
            'available_until' => null,
            // Add default values for other columns in your table
            'max_attempts' => 1,
            'shuffle_questions' => 0,
            'shuffle_options' => 0,
            'require_login' => 1,
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

        return redirect()->route('teacher.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        // Remove user filtering
        $quiz = Quiz::with(['questions.options'])
                   ->findOrFail($id);
        
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
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        // Remove user filtering
        $quiz = Quiz::with(['questions.options'])
                   ->findOrFail($id);
        
        $courses = Course::where('teacher_id', Auth::id())->get();
        return view('teacher.quizzes.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        // Remove user filtering
        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
            'is_published' => 'boolean',
            'duration' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            // Add validation for other fields
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'require_login' => 'boolean',
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

        return redirect()->route('teacher.quizzes.show', Crypt::encrypt($quiz->id))
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        // Remove user filtering
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return redirect()->route('teacher.quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    /**
     * Show the quiz taking page (preview mode for teacher)
     */
    public function take($encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            // Remove user filtering
            $quiz = Quiz::with(['questions.options'])
                       ->findOrFail($quizId);
            
            return view('teacher.quizzes.take', compact('quiz'));
        } catch (\Exception $e) {
            return redirect()->route('teacher.quizzes.index')
                ->with('error', 'Invalid quiz or quiz not found.');
        }
    }
    
    public function submit(Request $request, $encryptedId)
    {
        try {
            $quizId = Crypt::decrypt($encryptedId);
            // Remove user filtering
            $quiz = Quiz::with(['questions.options'])
                       ->findOrFail($quizId);
            
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
            return redirect()->route('teacher.quizzes.show', Crypt::encrypt($quiz->id))
                ->with('results', $results)
                ->with('score', $score)
                ->with('totalPoints', $totalPoints)
                ->with('percentage', $percentage)
                ->with('passed', $passed)
                ->with('success', 'Quiz submitted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('teacher.quizzes.index')
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
        return redirect()->route('teacher.quizzes.take', $encryptedId);
    }
    
    /**
     * Teacher-specific methods
     */
    
    public function togglePublish($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        // Remove user filtering
        $quiz = Quiz::findOrFail($id);
        
        $quiz->update([
            'is_published' => !$quiz->is_published
        ]);
        
        $status = $quiz->is_published ? 'published' : 'unpublished';
        return redirect()->back()
            ->with('success', "Quiz {$status} successfully.");
    }
    
    public function preview($encryptedId)
    {
        return $this->take($encryptedId);
    }
}