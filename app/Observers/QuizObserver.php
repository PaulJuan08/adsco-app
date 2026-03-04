<?php
namespace App\Observers;

use App\Models\Quiz;
use App\Models\QuizOption;

class QuizObserver
{
    /**
     * Automatically clean up all related records when a quiz is deleted.
     * Fires before the quiz row is removed from the database.
     */
    public function deleting(Quiz $quiz): void
    {
        // 1. Delete quiz options (children of questions)
        QuizOption::whereIn('question_id', $quiz->questions()->pluck('id'))->delete();

        // 2. Delete quiz questions
        $quiz->questions()->delete();

        // 3. Delete all student attempts for this quiz
        $quiz->attempts()->delete();

        // 4. Delete all student access records for this quiz
        $quiz->studentAccess()->delete();
    }
}
