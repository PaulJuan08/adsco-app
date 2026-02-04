<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 'description', 'content', 'video_link', 
        'attachment', 'course_id', 'order', 'estimated_time', 'is_published'
    ];
    
    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
        'estimated_time' => 'integer'
    ];
    
    // Direct course relationship (for backward compatibility)
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
    // Multiple courses relationship (through pivot table)
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_topics')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('course_topics.order');
    }
    
    // Get primary course (first course in the relationship)
    public function getPrimaryCourseAttribute()
    {
        return $this->courses->first() ?? $this->course;
    }
    
    // Progress relationship
    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
    
    // Get all students who completed this topic
    public function completedBy()
    {
        return $this->belongsToMany(User::class, 'progress', 'topic_id', 'student_id')
            ->wherePivot('status', 'completed')
            ->withPivot('completed_at', 'notes')
            ->withTimestamps();
    }
    
    // Scope for published topics
    public function scopePublished($query)
    {
        return $query->where('is_published', 1);
    }
    
    // Check if topic has video
    public function hasVideo()
    {
        return !empty($this->video_link);
    }
    
    // Check if topic has attachment
    public function hasAttachment()
    {
        return !empty($this->attachment);
    }
    
    // Get formatted estimated time
    public function getFormattedEstimatedTimeAttribute()
    {
        if (!$this->estimated_time) {
            return 'No time estimate';
        }
        
        return $this->estimated_time . ' minutes';
    }
    
    // Get topic order for a specific course
    public function getOrderForCourse($courseId)
    {
        $pivot = DB::table('course_topics')
            ->where('course_id', $courseId)
            ->where('topic_id', $this->id)
            ->first();
            
        return $pivot ? $pivot->order : 0;
    }
}