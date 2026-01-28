<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'course_code',
        'description',
        'teacher_id',
        'is_published', 
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationship with teacher
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Relationship with students
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student')
            ->withTimestamps();
    }

    // Accessor for status
    public function getStatusAttribute()
    {
        if (!$this->is_published) { // Changed to is_published
            return 'draft';
        }
        
        if ($this->start_date && $this->start_date > now()) {
            return 'upcoming';
        }
        
        if ($this->end_date && $this->end_date < now()) {
            return 'completed';
        }
        
        return 'active';
    }

    // Check if course is full
    public function getIsFullAttribute()
    {
        if (!$this->max_students) {
            return false;
        }
        
        $studentsCount = $this->students()->count();
        return $studentsCount >= $this->max_students;
    }

    // Scope for published courses
    public function scopePublished($query)
    {
        return $query->where('is_published', true); // Changed to is_published
    }

    // Scope for current semester
    public function scopeCurrentSemester($query)
    {
        return $query->where(function($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        });
    }

    // Get available seats
    public function getAvailableSeatsAttribute()
    {
        if (!$this->max_students) {
            return 'Unlimited';
        }
        
        $studentsCount = $this->students()->count();
        $available = $this->max_students - $studentsCount;
        
        return max(0, $available);
    }
}