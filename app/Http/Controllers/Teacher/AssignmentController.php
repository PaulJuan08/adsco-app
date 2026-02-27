<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AssignmentController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();
        $assignments = Assignment::where('created_by', $teacherId)
                               ->latest()
                               ->paginate(10);
        
        return view('teacher.assignments.index', compact('assignments'));
    }

    public function create()
    {
        return view('teacher.assignments.create');
    }

    public function store(Request $request)
    {
        $teacherId = Auth::id();
        
        $validated = $request->validate([
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

        // Add the teacher ID as the creator
        $validated['created_by'] = $teacherId;

        Assignment::create($validated);
        
        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $assignment = Assignment::where('created_by', $teacherId)
                               ->findOrFail($id);
        
        return view('teacher.assignments.show', compact('assignment'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $assignment = Assignment::where('created_by', $teacherId)
                               ->findOrFail($id);
        
        return view('teacher.assignments.edit', compact('assignment'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $assignment = Assignment::where('created_by', $teacherId)
                               ->findOrFail($id);

        $validated = $request->validate([
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
        
        $assignment = Assignment::where('created_by', $teacherId)
                               ->findOrFail($id);
        
        $assignment->delete();
        
        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();
        
        $assignment = Assignment::where('created_by', $teacherId)
                               ->findOrFail($id);
        
        $assignment->update([
            'is_published' => !$assignment->is_published
        ]);
        
        $status = $assignment->is_published ? 'published' : 'unpublished';
        
        return redirect()->back()->with('success', "Assignment {$status} successfully.");
    }
}