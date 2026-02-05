<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_points',
        'percentage',
        'passed',
        'time_taken',
        'started_at',
        'completed_at',
        'answers',
        'question_order',
        'option_order',
        'total_questions',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'answers' => 'array', // This casts JSON to array automatically
        'question_order' => 'array',
        'option_order' => 'array',
        'completed_at' => 'datetime',
        'started_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
}