<?php

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
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'points' => 'integer',
        'is_published' => 'boolean',
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
            ->exists();
    }
    
    public function isAvailable(): bool
    {
        return (bool) $this->is_published;
    }
    
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }
    
    public function canSubmit(int $studentId): bool
    {
        // Check if student has access
        if (!$this->isAccessibleByStudent($studentId)) {
            return false;
        }
        
        // Check if assignment is published
        if (!$this->is_published) {
            return false;
        }
        
        // Check if assignment is overdue - if overdue, cannot submit
        if ($this->isOverdue()) {
            return false;
        }
        
        return true;
    }
    
    public function getStatusForStudent(int $studentId): string
    {
        $submission = $this->submissions()->where('student_id', $studentId)->latest()->first();
        
        if (!$submission) {
            if ($this->isOverdue()) {
                return 'overdue';
            }
            return 'pending';
        }
        
        return $submission->status;
    }
}