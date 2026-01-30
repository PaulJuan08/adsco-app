<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['course', 'topic'])->latest()->paginate(10);
        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $courses = Course::all();
        $topics = Topic::all();
        return view('admin.assignments.create', compact('courses', 'topics'));
    }

    public function store(Request $request)
    {
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

        Assignment::create($validated);
        
        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::with(['course', 'topic'])->findOrFail($id);
        return view('admin.assignments.show', compact('assignment'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);
        $courses = Course::all();
        $topics = Topic::all();
        return view('admin.assignments.edit', compact('assignment', 'courses', 'topics'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);

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
        
        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();
        
        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }
}