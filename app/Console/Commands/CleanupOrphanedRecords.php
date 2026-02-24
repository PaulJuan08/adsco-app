<?php
// app/Console/Commands/CleanupOrphanedRecords.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentStudentAccess;
use App\Models\QuizAttempt;
use App\Models\QuizStudentAccess;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;

class CleanupOrphanedRecords extends Command
{
    protected $signature = 'cleanup:orphaned-records';
    protected $description = 'Delete records that reference non-existent users';

    public function handle()
    {
        $this->info('Cleaning up orphaned records...');

        // Assignment Submissions
        $deleted = AssignmentSubmission::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('users')
                  ->whereColumn('users.id', 'assignment_submissions.student_id');
        })->delete();
        $this->info("Deleted {$deleted} orphaned assignment submissions");

        // Assignment Student Access
        $deleted = AssignmentStudentAccess::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('users')
                  ->whereColumn('users.id', 'assignment_student_access.student_id');
        })->delete();
        $this->info("Deleted {$deleted} orphaned assignment student access records");

        // Quiz Attempts
        $deleted = QuizAttempt::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('users')
                  ->whereColumn('users.id', 'quiz_attempts.user_id');
        })->delete();
        $this->info("Deleted {$deleted} orphaned quiz attempts");

        // Quiz Student Access
        $deleted = QuizStudentAccess::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('users')
                  ->whereColumn('users.id', 'quiz_student_access.student_id');
        })->delete();
        $this->info("Deleted {$deleted} orphaned quiz student access records");

        // Progress
        $deleted = Progress::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('users')
                  ->whereColumn('users.id', 'progress.student_id');
        })->delete();
        $this->info("Deleted {$deleted} orphaned progress records");

        $this->info('Cleanup completed!');
    }
}