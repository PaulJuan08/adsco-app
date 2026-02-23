<?php
// app/Models/Assignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'topic_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'points',
        'attachment',
        'is_published',
        'available_from',
        'available_until',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'points' => 'integer',
        'is_published' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }
    
    public function studentAccess()
    {
        return $this->hasMany(AssignmentStudentAccess::class, 'assignment_id');
    }
    
    public function allowedStudents()
    {
        return $this->hasMany(AssignmentStudentAccess::class, 'assignment_id')->where('status', 'allowed');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
        $now = now();
        if ($this->available_from && $this->available_from > $now) return false;
        if ($this->available_until && $this->available_until < $now) return false;
        return (bool) $this->is_published;
    }
    
    public function getStatusForStudent(int $studentId): string
    {
        $submission = $this->submissions()->where('student_id', $studentId)->latest()->first();
        if (!$submission) return 'pending';
        return $submission->status;
    }
}