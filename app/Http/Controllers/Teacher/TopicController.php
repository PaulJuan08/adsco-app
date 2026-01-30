<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TopicController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();
        $topics = Topic::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->with('course')->latest()->paginate(10);
        
        return view('teacher.topics.index', compact('topics'));
    }

    public function create()
    {
        $teacherId = Auth::id();
        $courses = Course::where('teacher_id', $teacherId)->get();
        return view('teacher.topics.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $teacherId = Auth::id();
        
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer',
            'attachment' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        // Verify teacher owns the course
        $course = Course::where('id', $validated['course_id'])
                       ->where('teacher_id', $teacherId)
                       ->firstOrFail();

        Topic::create($validated);
        
        return redirect()->route('teacher.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $topic = Topic::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->with('course')->findOrFail($id);
        
        return view('teacher.topics.show', compact('topic'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $topic = Topic::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        $courses = Course::where('teacher_id', $teacherId)->get();
        return view('teacher.topics.edit', compact('topic', 'courses'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $topic = Topic::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer',
            'attachment' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $topic->update($validated);
        
        return redirect()->route('teacher.topics.index')
            ->with('success', 'Topic updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $topic = Topic::whereHas('course', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->findOrFail($id);
        
        $topic->delete();
        
        return redirect()->route('teacher.topics.index')
            ->with('success', 'Topic deleted successfully.');
    }
}