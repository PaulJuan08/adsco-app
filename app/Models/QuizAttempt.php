<?php
// app/Models/QuizAttempt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'total_points',
        'percentage',
        'passed',
        'answers',
        'completed_at',
        'total_questions',
        'time_taken',
        'started_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'completed_at' => 'datetime',
        'started_at' => 'datetime',
        'passed' => 'boolean',
        'percentage' => 'decimal:2',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}