<?php

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
        'course_id', // Add this if quizzes are course-specific
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
    
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
    /**
     * Check if quiz is available
     */
    public function isAvailable()
    {
        $now = now();
        
        if ($this->available_from && $this->available_from > $now) {
            return false;
        }
        
        if ($this->available_until && $this->available_until < $now) {
            return false;
        }
        
        return $this->is_published;
    }
    
    /**
     * Get time remaining
     */
    public function getTimeRemainingAttribute()
    {
        if (!$this->available_until) {
            return null;
        }
        
        return now()->diffInMinutes($this->available_until, false);
    }
}