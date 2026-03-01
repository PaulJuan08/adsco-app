<?php
// app/Console/Commands/CleanupOrphanedRecords.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentStudentAccess;
use App\Models\QuizAttempt;
use App\Models\QuizStudentAccess;
use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupOrphanedRecords extends Command
{
    protected $signature = 'cleanup:orphaned-records';
    protected $description = 'Delete records that reference non-existent users';

    public function handle()
    {
        $this->info('Starting cleanup of orphaned records...');
        $this->newLine();

        DB::transaction(function () {
            
            // 1. Clean up assignment submission files first
            $this->info('Cleaning up assignment submission files...');
            $submissions = AssignmentSubmission::whereDoesntHave('student')->get();
            $fileCount = 0;
            
            foreach ($submissions as $submission) {
                if ($submission->attachment_path && 
                    Storage::disk('public')->exists($submission->attachment_path)) {
                    Storage::disk('public')->delete($submission->attachment_path);
                    $fileCount++;
                }
            }
            $this->info("✓ Deleted {$fileCount} orphaned attachment files");
            $this->newLine();

            // 2. Quiz attempts
            $deleted = QuizAttempt::whereDoesntHave('user')->delete();
            $this->info("✓ Deleted {$deleted} orphaned quiz attempts");

            // 3. Quiz student access
            $deleted = QuizStudentAccess::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned quiz student access records");

            // 4. Assignment submissions
            $deleted = AssignmentSubmission::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned assignment submissions");

            // 5. Assignment student access
            $deleted = AssignmentStudentAccess::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned assignment student access records");

            // 6. Enrollments
            $deleted = Enrollment::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned enrollment records");

            // 7. Progress
            $deleted = Progress::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned progress records");

            // 8. For audit logs, set user_id to NULL instead of deleting
            $updated = DB::table('audit_logs')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('users')
                          ->whereRaw('users.id = audit_logs.user_id');
                })
                ->update(['user_id' => null]);
            
            $this->info("✓ Updated {$updated} audit logs to set user_id to NULL");
        });

        $this->newLine();
        $this->info('✅ Cleanup completed successfully!');
        
        // Show final counts
        $this->newLine();
        $this->table(['Table', 'Remaining Records'], [
            ['users', \App\Models\User::count()],
            ['quiz_attempts', QuizAttempt::count()],
            ['assignment_submissions', AssignmentSubmission::count()],
            ['enrollments', Enrollment::count()],
            ['progress', Progress::count()],
        ]);
    }
}