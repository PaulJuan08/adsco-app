<?php
// database/migrations/2024_xx_xx_xxxxxx_add_foreign_key_cascade_deletes.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop existing foreign keys if they exist
        $this->dropForeignKeys();

        // Add new foreign keys with CASCADE DELETE
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('quiz_student_access', function (Blueprint $table) {
            $table->foreign('student_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->foreign('student_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('assignment_student_access', function (Blueprint $table) {
            $table->foreign('student_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreign('student_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('progress', function (Blueprint $table) {
            $table->foreign('student_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        // For audit_logs, set to NULL instead of delete to keep history
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    private function dropForeignKeys()
    {
        $tables = [
            'quiz_attempts' => ['user_id'],
            'quiz_student_access' => ['student_id'],
            'assignment_submissions' => ['student_id'],
            'assignment_student_access' => ['student_id'],
            'enrollments' => ['student_id'],
            'progress' => ['student_id'],
            'audit_logs' => ['user_id'],
        ];

        foreach ($tables as $table => $columns) {
            try {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    foreach ($columns as $column) {
                        $table->dropForeign([$column]);
                    }
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }
    }

    public function down()
    {
        // Drop all foreign keys
        $tables = [
            'quiz_attempts', 'quiz_student_access', 'assignment_submissions',
            'assignment_student_access', 'enrollments', 'progress', 'audit_logs'
        ];

        foreach ($tables as $table) {
            try {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['user_id', 'student_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }
    }
};