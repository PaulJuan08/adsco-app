<?php
namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\AssignmentSubmission;

class UserObserver
{
    public function created(User $user)
    {
        if (auth()->check()) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'model_type' => 'User',
                'model_id' => $user->id,
                'new_values' => json_encode($user->getAttributes()),
                'remarks' => 'User account created',
                'ip_address' => request()->ip()
            ]);
        }
    }
    
    public function updated(User $user)
    {
        if (auth()->check()) {
            $changes = $user->getChanges();
            if (!empty($changes)) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'update',
                    'model_type' => 'User',
                    'model_id' => $user->id,
                    'old_values' => json_encode(array_intersect_key($user->getOriginal(), $changes)),
                    'new_values' => json_encode($changes),
                    'remarks' => 'User account updated',
                    'ip_address' => request()->ip()
                ]);
            }
        }
    }
    
    /**
     * Clean up attachment files before the DB cascade removes the submission rows.
     * Without this, the DB cascade deletes the rows but leaves the files on disk.
     */
    public function deleting(User $user): void
    {
        AssignmentSubmission::where('student_id', $user->id)
            ->each(fn($submission) => $submission->delete());
    }

    public function deleted(User $user)
    {
        if (auth()->check()) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'model_type' => 'User',
                'model_id' => $user->id,
                'old_values' => json_encode($user->getAttributes()),
                'remarks' => 'User account deleted',
                'ip_address' => request()->ip()
            ]);
        }
    }
}