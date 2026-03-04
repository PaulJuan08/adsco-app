<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quiz: drop available_from/available_until, add due_date
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'available_from')) {
                $table->dropColumn('available_from');
            }
            if (Schema::hasColumn('quizzes', 'available_until')) {
                $table->dropColumn('available_until');
            }
            if (!Schema::hasColumn('quizzes', 'due_date')) {
                $table->timestamp('due_date')->nullable()->after('passing_score');
            }
            if (!Schema::hasColumn('quizzes', 'duration')) {
                $table->integer('duration')->default(60)->after('passing_score');
            }
            if (!Schema::hasColumn('quizzes', 'passing_score')) {
                $table->integer('passing_score')->default(70)->after('total_questions');
            }
        });

        // Assignment: add duration and passing_score
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'duration')) {
                $table->integer('duration')->default(60)->after('points');
            }
            if (!Schema::hasColumn('assignments', 'passing_score')) {
                $table->integer('passing_score')->default(70)->after('duration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'due_date')) {
                $table->dropColumn('due_date');
            }
            if (!Schema::hasColumn('quizzes', 'available_from')) {
                $table->timestamp('available_from')->nullable();
                $table->timestamp('available_until')->nullable();
            }
        });

        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'duration')) {
                $table->dropColumn('duration');
            }
            if (Schema::hasColumn('assignments', 'passing_score')) {
                $table->dropColumn('passing_score');
            }
        });
    }
};
