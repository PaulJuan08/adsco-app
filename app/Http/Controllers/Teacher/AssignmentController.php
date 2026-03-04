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
            'duration' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:1|max:100',
            'attachment' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $validated['created_by'] = $teacherId;
        $validated['duration'] = $validated['duration'] ?? 60;
        $validated['passing_score'] = $validated['passing_score'] ?? 70;

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
        
        return view('teacher.assignments.edit', compact('assignment', 'encryptedId'));
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
            'duration' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:1|max:100',
            'attachment' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $validated['duration'] = $validated['duration'] ?? $assignment->duration ?? 60;
        $validated['passing_score'] = $validated['passing_score'] ?? $assignment->passing_score ?? 70;

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