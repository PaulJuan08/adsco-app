<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class QuizController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();
        
        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('student_id', $studentId)
            ->where('status', 'active')
            ->pluck('course_id')
            ->toArray();

        $quizzes = Quiz::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', 1)
            ->with('course')
            ->latest()
            ->paginate(10);
        
        return view('student.quizzes.index', compact('quizzes'));
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $studentId = Auth::id();
        
        $quiz = Quiz::with('course')->findOrFail($id);
        
        // Check if student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $studentId)
            ->where('course_id', $quiz->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        if (!$quiz->is_published) {
            abort(404, 'Quiz is not available.');
        }
        
        // Check if quiz is available based on dates
        $now = now();
        if ($quiz->available_from && $now->lt($quiz->available_from)) {
            abort(403, 'Quiz is not yet available.');
        }
        
        if ($quiz->available_until && $now->gt($quiz->available_until)) {
            abort(403, 'Quiz deadline has passed.');
        }
        
        return view('student.quizzes.show', compact('quiz'));
    }

    public function submit(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $studentId = Auth::id();
        
        $quiz = Quiz::findOrFail($id);
        
        // Check enrollment
        $isEnrolled = Enrollment::where('student_id', $studentId)
            ->where('course_id', $quiz->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        if (!$quiz->is_published) {
            abort(404, 'Quiz is not available.');
        }
        
        // Validate submission data
        $request->validate([
            'answers' => 'required|array',
            'time_taken' => 'required|integer',
        ]);
        
        // Here you would process the quiz submission
        // Calculate score, save attempt, etc.
        
        return redirect()->route('student.quizzes.show', $encryptedId)
            ->with('success', 'Quiz submitted successfully!');
    }
}