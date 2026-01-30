<?php

// app/Models/Quiz.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_published',
        'duration',
        'total_questions',
        'passing_score',
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}