<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Check if the teacher has access to an assignment.
     * Access is granted if:
     * 1. The teacher created it (created_by), OR
     * 2. The assignment belongs to a course the teacher is assigned to (primary or co-teacher)
     */
    private function teacherAssignmentQuery($teacherId)
    {
        return Assignment::where(function ($q) use ($teacherId) {
            $q->where('created_by', $teacherId)
              ->orWhereHas('course', function ($cq) use ($teacherId) {
                  $cq->where('teacher_id', $teacherId)
                     ->orWhereHas('teachers', function ($tq) use ($teacherId) {
                         $tq->where('users.id', $teacherId);
                     });
              });
        });
    }

    public function index(Request $request)
    {
        $teacherId = Auth::id();
        $search    = $request->get('search', '');

        $assignments = $this->teacherAssignmentQuery($teacherId)
            ->with(['course', 'creator'])
            ->withCount(['allowedStudents as allowed_students_count'])
            ->withCount('submissions')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)->withQueryString();

        $totalAssignments = $this->teacherAssignmentQuery($teacherId)->count();
        $publishedCount   = $this->teacherAssignmentQuery($teacherId)->where('is_published', 1)->count();
        $pendingReviews   = \App\Models\AssignmentSubmission::whereHas('assignment', function ($q) use ($teacherId) {
            $q->where('created_by', $teacherId);
        })->whereIn('status', ['submitted', 'late'])->count();

        return view('teacher.assignments.index', compact('assignments', 'search', 'totalAssignments', 'publishedCount', 'pendingReviews'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $html = view('teacher.assignments._form', [
                'editing' => false,
                'formAction' => route('teacher.assignments.store'),
                'assignment' => null,
            ])->render();
            return response()->json(['html' => $html]);
        }

        return redirect()->route('teacher.assignments.index');
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
        $validated['updated_by'] = $teacherId;
        $validated['duration'] = $validated['duration'] ?? 60;
        $validated['passing_score'] = $validated['passing_score'] ?? 70;

        Assignment::create($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Assignment created successfully.', 'redirect' => route('teacher.assignments.index')]);
        }

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function show($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();

        $assignment = $this->teacherAssignmentQuery($teacherId)
                          ->with('creator', 'updater')
                          ->findOrFail($id);

        return view('teacher.assignments.show', compact('assignment'));
    }

    public function edit(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();

        $assignment = $this->teacherAssignmentQuery($teacherId)
                          ->findOrFail($id);

        if ($request->ajax()) {
            $html = view('teacher.assignments._form', [
                'editing' => true,
                'formAction' => route('teacher.assignments.update', $encryptedId),
                'assignment' => $assignment,
            ])->render();
            return response()->json(['html' => $html]);
        }

        return redirect()->route('teacher.assignments.index');
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();

        $assignment = $this->teacherAssignmentQuery($teacherId)
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

        $validated['updated_by'] = $teacherId;
        $validated['duration'] = $validated['duration'] ?? $assignment->duration ?? 60;
        $validated['passing_score'] = $validated['passing_score'] ?? $assignment->passing_score ?? 70;

        $assignment->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Assignment updated successfully.', 'redirect' => route('teacher.assignments.index')]);
        }

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();

        $assignment = $this->teacherAssignmentQuery($teacherId)
                          ->findOrFail($id);

        // Clean up submission files and access records before delete
        foreach ($assignment->submissions as $submission) {
            if ($submission->attachment_path && Storage::disk('public')->exists($submission->attachment_path)) {
                Storage::disk('public')->delete($submission->attachment_path);
            }
        }
        DB::table('assignment_student_access')->where('assignment_id', $id)->delete();
        $assignment->submissions()->delete();

        $assignment->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Assignment deleted successfully.']);
        }
        return redirect()->route('teacher.assignments.index')->with('success', 'Assignment deleted successfully.');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $teacherId = Auth::id();

        $assignment = $this->teacherAssignmentQuery($teacherId)
                          ->findOrFail($id);

        $assignment->update([
            'is_published' => !$assignment->is_published,
            'updated_by' => $teacherId,
        ]);

        $status = $assignment->is_published ? 'published' : 'unpublished';
        $msg = "Assignment {$status} successfully.";

        if (request()->ajax()) {
            return response()->json(['message' => $msg]);
        }
        return redirect()->route('teacher.assignments.index')->with('success', $msg);
    }
}