<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentStudentAccess;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for the student
     */
    public function index(Request $request)
    {
        $studentId = Auth::id();
        $filter = $request->get('filter', 'all');
        
        // Get assignments the student has access to
        $allowedAssignmentIds = AssignmentStudentAccess::where('student_id', $studentId)
            ->where('status', 'allowed')
            ->pluck('assignment_id');

        $query = Assignment::whereIn('id', $allowedAssignmentIds)
            ->where('is_published', 1)
            ->with('course', 'topic');

        // Apply filters
        switch ($filter) {
            case 'pending':
                $query->whereDoesntHave('submissions', function($q) use ($studentId) {
                    $q->where('student_id', $studentId);
                })->orWhereHas('submissions', function($q) use ($studentId) {
                    $q->where('student_id', $studentId)->where('status', 'pending');
                });
                break;
                
            case 'submitted':
                $query->whereHas('submissions', function($q) use ($studentId) {
                    $q->where('student_id', $studentId)
                      ->whereIn('status', ['submitted', 'late']);
                });
                break;
                
            case 'graded':
                $query->whereHas('submissions', function($q) use ($studentId) {
                    $q->where('student_id', $studentId)
                      ->where('status', 'graded');
                });
                break;
                
            case 'overdue':
                $query->whereDoesntHave('submissions', function($q) use ($studentId) {
                    $q->where('student_id', $studentId);
                })->where('due_date', '<', now());
                break;
        }

        $assignments = $query->latest()->paginate(10);

        // Add submission data to each assignment
        foreach ($assignments as $assignment) {
            $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('student_id', $studentId)
                ->latest()
                ->first();
            
            $assignment->my_submission = $submission;
            $assignment->can_submit = $assignment->canSubmit($studentId);
            $assignment->status_for_student = $assignment->getStatusForStudent($studentId);
        }

        return view('student.assignments.index', compact('assignments'));
    }

    /**
     * Display the specified assignment
     */
    public function show($encryptedId)
    {
        $studentId = Auth::id();
        $assignmentId = Crypt::decrypt($encryptedId);

        // Check if student has access
        abort_unless(
            AssignmentStudentAccess::where('assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->where('status', 'allowed')
                ->exists(),
            403, 'You do not have access to this assignment.'
        );

        $assignment = Assignment::with(['course', 'topic'])
            ->findOrFail($assignmentId);

        // Check if assignment is published
        abort_if(!$assignment->is_published, 403, 'This assignment is not available.');

        // Get student's submission
        $submission = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->latest()
            ->first();

        // Check if student can submit
        $canSubmit = $assignment->canSubmit($studentId);
        
        // If assignment is overdue and no submission exists, show appropriate message
        if ($assignment->isOverdue() && !$submission) {
            session()->flash('error', 'This assignment is overdue and can no longer be submitted. Please contact your instructor if you need an extension.');
        }

        return view('student.assignments.show', compact('assignment', 'submission', 'encryptedId', 'canSubmit'));
    }

    /**
     * Submit assignment
     */
    public function submit(Request $request, $encryptedId)
    {
        $studentId = Auth::id();
        $assignmentId = Crypt::decrypt($encryptedId);

        $assignment = Assignment::findOrFail($assignmentId);

        // Check if student has access
        abort_unless(
            AssignmentStudentAccess::where('assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->where('status', 'allowed')
                ->exists(),
            403
        );

        // Check if assignment can be submitted
        if (!$assignment->canSubmit($studentId)) {
            if ($assignment->isOverdue()) {
                return back()->with('error', 'This assignment is overdue and can no longer be submitted. Please contact your instructor if you need an extension.');
            }
            return back()->with('error', 'You cannot submit this assignment at this time.');
        }

        $request->validate([
            'answer_text' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Check if at least one field is provided
        if (!$request->answer_text && !$request->hasFile('attachment')) {
            return back()->with('error', 'Please provide either an answer text or upload a file.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('assignments/submissions', 'public');
        }

        $isLate = $assignment->due_date && now()->isAfter($assignment->due_date);

        // Check for existing submission
        $existingSubmission = AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();

        if ($existingSubmission) {
            // Delete old attachment if exists
            if ($existingSubmission->attachment_path && Storage::disk('public')->exists($existingSubmission->attachment_path)) {
                Storage::disk('public')->delete($existingSubmission->attachment_path);
            }

            // Update existing submission
            $existingSubmission->update([
                'answer_text' => $request->answer_text,
                'attachment_path' => $attachmentPath,
                'status' => $isLate ? 'late' : 'submitted',
                'submitted_at' => now(),
            ]);

            $message = 'Assignment resubmitted successfully.';
        } else {
            // Create new submission
            AssignmentSubmission::create([
                'assignment_id' => $assignmentId,
                'student_id' => $studentId,
                'answer_text' => $request->answer_text,
                'attachment_path' => $attachmentPath,
                'status' => $isLate ? 'late' : 'submitted',
                'submitted_at' => now(),
            ]);

            $message = 'Assignment submitted successfully.';
        }

        return redirect()->route('student.assignments.show', $encryptedId)
            ->with('success', $message);
    }
}