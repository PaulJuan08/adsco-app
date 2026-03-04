<?php
namespace App\Observers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Storage;

class AssignmentObserver
{
    /**
     * Automatically clean up all related records and files when an assignment is deleted.
     * Fires before the assignment row is removed from the database.
     */
    public function deleting(Assignment $assignment): void
    {
        // 1. Delete each submission individually so AssignmentSubmission's own
        //    deleting event fires, which handles the attachment file cleanup.
        $assignment->submissions()->each(fn($submission) => $submission->delete());

        // 2. Delete all student access records for this assignment
        $assignment->studentAccess()->delete();

        // 3. Delete the assignment's own attachment file if it exists
        if ($assignment->attachment && Storage::disk('public')->exists($assignment->attachment)) {
            Storage::disk('public')->delete($assignment->attachment);
        }
    }
}
