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
use App\Models\Quiz;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupOrphanedRecords extends Command
{
    protected $signature = 'cleanup:orphaned-records {--all : Clean up all orphaned records including content references}';
    protected $description = 'Delete records that reference non-existent users, quizzes, or assignments';

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

            // 2. Quiz attempts - check for non-existent users
            $deleted = QuizAttempt::whereDoesntHave('user')->delete();
            $this->info("✓ Deleted {$deleted} orphaned quiz attempts (missing users)");

            // 3. Quiz attempts - check for non-existent quizzes
            if ($this->option('all') || $this->confirm('Do you want to delete quiz attempts for deleted quizzes?')) {
                $deleted = QuizAttempt::whereDoesntHave('quiz')->delete();
                $this->info("✓ Deleted {$deleted} orphaned quiz attempts (missing quizzes)");
            } else {
                $orphanedQuizAttempts = QuizAttempt::whereDoesntHave('quiz')->count();
                $this->warn("⚠ Skipped: {$orphanedQuizAttempts} quiz attempts for deleted quizzes (use --all to delete)");
            }

            // 4. Quiz student access - check for non-existent users
            $deleted = QuizStudentAccess::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned quiz student access records (missing users)");

            // 5. Quiz student access - check for non-existent quizzes
            if ($this->option('all')) {
                $deleted = QuizStudentAccess::whereDoesntHave('quiz')->delete();
                $this->info("✓ Deleted {$deleted} orphaned quiz student access records (missing quizzes)");
            }

            // 6. Assignment submissions - check for non-existent students
            $deleted = AssignmentSubmission::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned assignment submissions (missing students)");

            // 7. Assignment submissions - check for non-existent assignments
            if ($this->option('all')) {
                $deleted = AssignmentSubmission::whereDoesntHave('assignment')->delete();
                $this->info("✓ Deleted {$deleted} orphaned assignment submissions (missing assignments)");
            }

            // 8. Assignment student access - check for non-existent students
            $deleted = AssignmentStudentAccess::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned assignment student access records (missing students)");

            // 9. Assignment student access - check for non-existent assignments
            if ($this->option('all')) {
                $deleted = AssignmentStudentAccess::whereDoesntHave('assignment')->delete();
                $this->info("✓ Deleted {$deleted} orphaned assignment student access records (missing assignments)");
            }

            // 10. Enrollments
            $deleted = Enrollment::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned enrollment records");

            // 11. Progress
            $deleted = Progress::whereDoesntHave('student')->delete();
            $this->info("✓ Deleted {$deleted} orphaned progress records");

            // 12. For audit logs, set user_id to NULL instead of deleting
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
            ['quizzes', Quiz::count()],
            ['assignments', Assignment::count()],
            ['quiz_attempts', QuizAttempt::count()],
            ['quiz_access', QuizStudentAccess::count()],
            ['assignment_submissions', AssignmentSubmission::count()],
            ['assignment_access', AssignmentStudentAccess::count()],
            ['enrollments', Enrollment::count()],
            ['progress', Progress::count()],
        ]);
    }
}