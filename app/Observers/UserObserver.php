<?php
namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;

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