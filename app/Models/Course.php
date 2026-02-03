<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'title',
        'description',
        'teacher_id',
        'is_published',
        'thumbnail',
        'credits',
        'status',
        'start_date',
        'end_date',
        'max_students' // Added this as it's referenced in your methods
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'max_students' => 'integer'
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

    // Relationship with topics - ordered by 'order' field then creation date
    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'course_topics')
                    ->withTimestamps()
                    ->withPivot(['order']); // Add pivot columns if needed
    }

    // Relationship with enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

    // Accessor for status
    public function getStatusAttribute()
    {
        if (!$this->is_published) {
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

    // Accessor: Check if course is full
    public function getIsFullAttribute()
    {
        if (!$this->max_students) {
            return false;
        }
        
        $studentsCount = $this->students()->count();
        return $studentsCount >= $this->max_students;
    }

    // Accessor: Get available seats
    public function getAvailableSeatsAttribute()
    {
        if (!$this->max_students) {
            return 'Unlimited';
        }
        
        $studentsCount = $this->students()->count();
        $available = $this->max_students - $studentsCount;
        
        return max(0, $available);
    }

    // Accessor: Get formatted credits
    public function getFormattedCreditsAttribute()
    {
        return $this->credits . ' ' . ($this->credits == 1 ? 'Credit' : 'Credits');
    }

    // Scope for published courses
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope for draft courses
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    // Scope for current semester (active or upcoming)
    public function scopeCurrentSemester($query)
    {
        return $query->where(function($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        });
    }

    // Scope for courses taught by specific teacher
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // Scope for searching courses
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('course_code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Get the count of published topics
    public function getPublishedTopicsCountAttribute()
    {
        return $this->topics()->where('is_published', true)->count();
    }

    // Get the count of all topics
    public function getTopicsCountAttribute()
    {
        return $this->topics()->count();
    }

    // Check if course has topics
    public function getHasTopicsAttribute()
    {
        return $this->topics()->exists();
    }

    // Get next topic order number
    public function getNextTopicOrderAttribute()
    {
        $lastTopic = $this->topics()->orderByDesc('order')->first();
        return $lastTopic ? $lastTopic->order + 1 : 1;
    }

    // Helper method to publish the course
    public function publish()
    {
        $this->update(['is_published' => true]);
        return $this;
    }

    // Helper method to unpublish the course
    public function unpublish()
    {
        $this->update(['is_published' => false]);
        return $this;
    }

    // Helper method to check if student is enrolled
    public function isEnrolled($studentId)
    {
        return $this->students()->where('user_id', $studentId)->exists();
    }

    // Get course duration in weeks (if start_date and end_date are set)
    public function getDurationInWeeksAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }
        
        $diff = $this->start_date->diffInDays($this->end_date);
        return ceil($diff / 7);
    }

    // Add this method to get enrolled students count
    public function getEnrolledStudentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

}