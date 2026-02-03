<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_questions',
        'percentage',
        'passed',
        'time_taken',
        'completed_at',
        'answers',
    ];

    protected $casts = [
        'score' => 'integer',
        'total_questions' => 'integer',
        'percentage' => 'float',
        'passed' => 'boolean',
        'time_taken' => 'integer',
        'completed_at' => 'datetime',
        'answers' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
    
    /**
     * Get formatted time taken
     */
    public function getFormattedTimeTakenAttribute()
    {
        if ($this->time_taken < 60) {
            return $this->time_taken . ' seconds';
        }
        
        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;
        
        if ($seconds > 0) {
            return $minutes . 'm ' . $seconds . 's';
        }
        
        return $minutes . ' minutes';
    }
    
    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        if ($this->passed) {
            return 'badge-success';
        } else {
            return 'badge-danger';
        }
    }
    
    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return $this->passed ? 'Passed' : 'Failed';
    }
}