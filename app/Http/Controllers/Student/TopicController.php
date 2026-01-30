<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TopicController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();
        
        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('student_id', $studentId)
            ->where('status', 'active')
            ->pluck('course_id')
            ->toArray();

        $topics = Topic::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', 1)
            ->with('course')
            ->latest()
            ->paginate(10);
        
        return view('student.topics.index', compact('topics'));
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $studentId = Auth::id();
        
        // Check if student is enrolled in the course
        $topic = Topic::with('course')->findOrFail($id);
        
        $isEnrolled = Enrollment::where('student_id', $studentId)
            ->where('course_id', $topic->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        if (!$topic->is_published) {
            abort(404, 'Topic is not available.');
        }
        
        return view('student.topics.show', compact('topic'));
    }
}