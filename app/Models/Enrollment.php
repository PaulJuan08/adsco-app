<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'enrolled_at',
        'grade',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'grade' => 'float',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
    /**
     * Scope for active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->orWhereNull('status');
    }
    
    /**
     * Scope for completed enrollments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
                    ->orWhereNotNull('grade');
    }
    
    /**
     * Check if enrollment is active
     */
    public function isActive()
    {
        return $this->status === 'active' || $this->status === null;
    }
    
    /**
     * Check if enrollment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed' || $this->grade !== null;
    }
    
    /**
     * Get grade letter
     */
    public function getGradeLetterAttribute()
    {
        if ($this->grade === null) {
            return 'N/A';
        }
        
        if ($this->grade >= 90) return 'A';
        if ($this->grade >= 80) return 'B';
        if ($this->grade >= 70) return 'C';
        if ($this->grade >= 60) return 'D';
        return 'F';
    }
}