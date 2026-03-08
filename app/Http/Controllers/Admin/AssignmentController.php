<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\AssignmentSubmission;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $assignments = Assignment::with(['course', 'creator'])
            ->withCount(['allowedStudents as allowed_students_count'])
            ->withCount('submissions')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15);

        $totalAssignments = Assignment::count();
        $publishedCount   = Assignment::where('is_published', 1)->count();
        $pendingReviews   = AssignmentSubmission::whereIn('status', ['submitted', 'late'])->count();

        return view('admin.assignments.index', compact('assignments', 'search', 'totalAssignments', 'publishedCount', 'pendingReviews'));
    }

    public function create(Request $request)
    {
        $courses = Course::all();
        $topics = Topic::all();

        if ($request->ajax()) {
            $html = view('admin.assignments._form', [
                'editing' => false,
                'formAction' => route('admin.assignments.store'),
                'assignment' => null,
                'courses' => $courses,
                'topics' => $topics,
            ])->render();
            return response()->json(['html' => $html]);
        }

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
            'duration' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:1|max:100',
            'is_published' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        $validated['duration'] = $validated['duration'] ?? 60;
        $validated['passing_score'] = $validated['passing_score'] ?? 70;

        Assignment::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Assignment created successfully.', 'redirect' => route('admin.todo.index', ['type' => 'assignment'])]);
        }

        // Redirect to To-Do with assignment filter
        return redirect()->route('admin.todo.index', ['type' => 'assignment'])
            ->with('success', 'Assignment created successfully.');
    }

    // REMOVED show() method - now handled by TodoController

    public function edit(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);
        $courses = Course::all();
        $topics = Topic::all();

        if ($request->ajax()) {
            $html = view('admin.assignments._form', [
                'editing' => true,
                'formAction' => route('admin.assignments.update', $encryptedId),
                'assignment' => $assignment,
                'courses' => $courses,
                'topics' => $topics,
            ])->render();
            return response()->json(['html' => $html]);
        }

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
            'duration' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:1|max:100',
            'is_published' => 'boolean',
        ]);

        $validated['duration'] = $validated['duration'] ?? $assignment->duration ?? 60;
        $validated['passing_score'] = $validated['passing_score'] ?? $assignment->passing_score ?? 70;
        $validated['updated_by'] = auth()->id();

        $assignment->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Assignment updated successfully.', 'redirect' => route('admin.assignments.index')]);
        }

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Assignment deleted successfully.']);
        }
        return redirect()->route('admin.todo.index', ['type' => 'assignment'])->with('success', 'Assignment deleted successfully.');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'is_published' => !$assignment->is_published,
            'updated_by'   => auth()->id(),
        ]);

        $status = $assignment->is_published ? 'published' : 'unpublished';
        $msg = "Assignment {$status} successfully.";

        if (request()->ajax()) {
            return response()->json(['message' => $msg]);
        }
        return redirect()->route('admin.assignments.index')->with('success', $msg);
    }
}