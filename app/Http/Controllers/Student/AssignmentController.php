<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AssignmentController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();
        
        // Get enrolled course IDs
        $enrolledCourseIds = Enrollment::where('student_id', $studentId)
            ->where('status', 'active')
            ->pluck('course_id')
            ->toArray();

        $assignments = Assignment::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', 1)
            ->with(['course', 'topic'])
            ->latest()
            ->paginate(10);
        
        return view('student.assignments.index', compact('assignments'));
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $studentId = Auth::id();
        
        $assignment = Assignment::with(['course', 'topic'])->findOrFail($id);
        
        // Check if student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $studentId)
            ->where('course_id', $assignment->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        if (!$assignment->is_published) {
            abort(404, 'Assignment is not available.');
        }
        
        // Check if assignment is available based on dates
        $now = now();
        if ($assignment->available_from && $now->lt($assignment->available_from)) {
            abort(403, 'Assignment is not yet available.');
        }
        
        if ($assignment->available_until && $now->gt($assignment->available_until)) {
            abort(403, 'Assignment deadline has passed.');
        }
        
        return view('student.assignments.show', compact('assignment'));
    }
}