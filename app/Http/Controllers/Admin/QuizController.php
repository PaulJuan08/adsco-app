<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('course')->latest()->paginate(10);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('admin.quizzes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:1',
            'is_published' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date',
        ]);

        Quiz::create($validated);
        
        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $quiz = Quiz::with('course')->findOrFail($id);
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $quiz = Quiz::findOrFail($id);
        $courses = Course::all();
        return view('admin.quizzes.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:1',
            'is_published' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date',
        ]);

        $quiz->update($validated);
        
        return redirect()->route('admin.quizzes.index')
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
}