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
    public function create()
    {
        $courses = Course::all();
        $topics = Topic::all();
        return view('admin.assignments.create', compact('courses', 'topics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'points' => 'required|integer|min:1',
            'is_published' => 'boolean',
        ]);

        $assignment = Assignment::create($validated);
        
        // Redirect to To-Do with assignment filter
        return redirect()->route('admin.todo.index', ['type' => 'assignment'])
            ->with('success', 'Assignment created successfully.');
    }

    // REMOVED show() method - now handled by TodoController

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);
        $courses = Course::all();
        $topics = Topic::all();
        
        return view('admin.assignments.edit', compact('assignment', 'courses', 'topics', 'encryptedId'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'points' => 'required|integer|min:1',
            'is_published' => 'boolean',
        ]);

        $assignment->update($validated);
        
        return redirect()->route('admin.todo.index', ['type' => 'assignment'])
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();
        
        return redirect()->route('admin.todo.index', ['type' => 'assignment'])
            ->with('success', 'Assignment deleted successfully.');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);
        
        $assignment->update([
            'is_published' => !$assignment->is_published
        ]);
        
        $status = $assignment->is_published ? 'published' : 'unpublished';
        
        return redirect()->back()->with('success', "Assignment {$status} successfully.");
    }
}