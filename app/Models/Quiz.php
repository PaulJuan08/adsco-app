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
        'due_date',
        'course_id',
        'user_id',
        'created_by',
        'updated_by',
        'user_type',
        'shuffle_questions',
        'shuffle_options',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'due_date' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
    
    public function latestAttemptByUser($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();
    }
    
    public function allAttemptsByUser($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get();
    }
    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    public function studentAccess()
    {
        return $this->hasMany(QuizStudentAccess::class, 'quiz_id');
    }
    
    public function allowedStudents()
    {
        return $this->hasMany(QuizStudentAccess::class, 'quiz_id')->where('status', 'allowed');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function isAccessibleByStudent(int $studentId): bool
    {
        return $this->studentAccess()
            ->where('student_id', $studentId)
            ->where('status', 'allowed')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }
    
    public function isAvailable(): bool
    {
        if (!$this->is_published) return false;
        if ($this->due_date && $this->due_date->isPast()) return false;
        return true;
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }
}