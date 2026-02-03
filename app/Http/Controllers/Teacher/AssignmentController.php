<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AssignmentController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();
        $assignments = Assignment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->with(['course', 'topic'])->latest()->paginate(10);
        
        return view('teacher.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $teacherId = Auth::id();
        $courses = Course::where('teacher_id', $teacherId)->get();
        $topics = Topic::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->get();
        
        return view('teacher.assignments.create', compact('courses', 'topics'));
    }

    public function store(Request $request)
    {
        $teacherId = Auth::id();
        
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'points' => 'required|integer|min:1',
            'attachment' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date',
        ]);

        // Verify teacher owns the course
        $course = Course::where('id', $validated['course_id'])
                       ->where('teacher_id', $teacherId)
                       ->firstOrFail();

        Assignment::create($validated);
        
        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $assignment = Assignment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->with(['course', 'topic'])->findOrFail($id);
        
        return view('teacher.assignments.show', compact('assignment'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $assignment = Assignment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        $courses = Course::where('teacher_id', $teacherId)->get();
        $topics = Topic::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->get();
        
        return view('teacher.assignments.edit', compact('assignment', 'courses', 'topics'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $assignment = Assignment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'points' => 'required|integer|min:1',
            'attachment' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date',
        ]);

        $assignment->update($validated);
        
        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $assignment = Assignment::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        $assignment->delete();
        
        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }
}