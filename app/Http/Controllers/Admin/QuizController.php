<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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

        // Debug form submission
        \Log::info('========== FORM SUBMISSION DEBUG ==========');
        \Log::info('Request method: ' . $request->method());
        \Log::info('All request data:', $request->all());

        if ($request->has('questions')) {
            \Log::info('Number of questions: ' . count($request->questions));
            
            foreach ($request->questions as $qIndex => $question) {
                \Log::info("Question $qIndex text: " . ($question['question'] ?? 'empty'));
                
                if (isset($question['options'])) {
                    \Log::info("Question $qIndex has " . count($question['options']) . " options:");
                    foreach ($question['options'] as $oIndex => $option) {
                        \Log::info("  Option $oIndex: " . print_r($option, true));
                    }
                }
                
                \Log::info("Correct answer index: " . ($question['correct_answer'] ?? 'not set'));
            }
        }

        // Debug: Log ALL form data
        \Log::info('=== FORM SUBMISSION DATA ===');
        \Log::info('Full request data:', $request->all());
        
        // Specifically log questions and options
        if ($request->has('questions')) {
            foreach ($request->questions as $qIndex => $questionData) {
                \Log::info("Question $qIndex data:", $questionData);
                
                if (isset($questionData['options'])) {
                    \Log::info("Question $qIndex options count: " . count($questionData['options']));
                    \Log::info("Question $qIndex options:", $questionData['options']);
                }
            }
        }

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
                    'points' => 1, // Always 1 point
                    'order' => $questionOrder++,
                    'explanation' => $questionData['explanation'] ?? null,
                ]);

                // Save options
                if (isset($questionData['options'])) {
                    $optionOrder = 1;
                    $correctAnswerIndex = $questionData['correct_answer'] ?? null;
                    
                    \Log::info("Processing options for question. Correct answer index: $correctAnswerIndex");
                    \Log::info("Options array:", $questionData['options']);
                    
                    foreach ($questionData['options'] as $optionIndex => $optionData) {
                        \Log::info("Processing option index: $optionIndex");
                        \Log::info("Option data:", [$optionData]);
                        
                        // Skip if option text is empty
                        if (empty($optionData['option_text'])) {
                            \Log::info("Skipping option $optionIndex - empty text");
                            continue;
                        }
                        
                        // Determine if this option is correct
                        $isCorrect = ($optionIndex == $correctAnswerIndex);
                        
                        \Log::info("Creating option: text='{$optionData['option_text']}', is_correct=$isCorrect, order=$optionOrder");
                        
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

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        
        // Debug: Check what data we're getting
        \Log::info('Quiz show method called for quiz ID: ' . $id);
        
        foreach ($quiz->questions as $index => $question) {
            \Log::info('Question ' . ($index + 1) . ' ID: ' . $question->id);
            \Log::info('Options count: ' . $question->options->count());
            \Log::info('Options details: ' . json_encode($question->options->map(function($option) {
                return [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                    'order' => $option->order
                ];
            })));
        }
        
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
            
            // Update quiz basic info
            $quiz->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'total_questions' => $request->total_questions,
                'passing_score' => $request->passing_score,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
            ]);
            
            // Process questions
            if ($request->has('questions')) {
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
                            ]);
                            
                            // Process options for this question
                            $this->processQuestionOptions($question, $questionData);
                        }
                    } else {
                        // Create new question
                        $question = QuizQuestion::create([
                            'quiz_id' => $quiz->id,
                            'question' => $questionData['question'],
                            'explanation' => $questionData['explanation'] ?? null,
                        ]);
                        
                        // Process options for new question
                        $this->processQuestionOptions($question, $questionData);
                    }
                }
                
                // Update total questions count
                $quiz->update([
                    'total_questions' => QuizQuestion::where('quiz_id', $quiz->id)->count()
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.quizzes.show', Crypt::encrypt($quiz->id))
                ->with('success', 'Quiz updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
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
                    $option = QuizOption::find($optionData['id']);
                    if ($option && $option->quiz_question_id == $question->id) {
                        $option->update([
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $isCorrect ? 1 : 0
                        ]);
                        $processedOptionIds[] = $option->id;
                    }
                } else {
                    // Create new option
                    $option = QuizOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $isCorrect ? 1 : 0
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
        
        // Process each option
        foreach ($options as $optionIndex => $optionData) {
            // Skip if option text is empty
            if (empty($optionData['option_text'])) {
                continue;
            }
            
            if (isset($optionData['id']) && !empty($optionData['id'])) {
                // Update existing option
                $option = QuizOption::find($optionData['id']);
                if ($option) {
                    $option->update([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => ($correctAnswerIndex == $optionIndex) ? 1 : 0
                    ]);
                    $submittedOptionIds[] = $option->id;
                }
            } else {
                // Create new option
                $option = QuizOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['option_text'],
                    'is_correct' => ($correctAnswerIndex == $optionIndex) ? 1 : 0
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
        foreach ($options as $optionIndex => $optionData) {
            // Skip if option text is empty
            if (empty($optionData['option_text'])) {
                continue;
            }
            
            QuizOption::create([
                'question_id' => $question->id,
                'option_text' => $optionData['option_text'],
                'is_correct' => ($correctAnswerIndex == $optionIndex) ? 1 : 0
            ]);
        }
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
                $totalPoints += 1; // Always 1 point per question
                $userAnswer = $request->input("question_{$question->id}");
                
                $correctOption = $question->options->where('is_correct', true)->first();
                $isCorrect = false;
                
                // Check if user selected the correct option
                if ($correctOption && $userAnswer == $correctOption->id) {
                    $isCorrect = true;
                    $score += 1; // Always 1 point for correct answer
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