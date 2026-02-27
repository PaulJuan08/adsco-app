<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Topic extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 'description', 'content', 'video_link', 
        'attachment', 'pdf_file', 'course_id', 'order', 
        'estimated_time', 'is_published', 'created_by'  // Add created_by here
    ];
    
    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
        'estimated_time' => 'integer'
    ];
    
    // ============ NEW: Creator Relationship ============
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Helper to get creator name
    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->f_name . ' ' . $this->creator->l_name : 'System';
    }
    
    // Helper to get creator avatar
    public function getCreatorAvatarAttribute()
    {
        if (!$this->creator) {
            return 'S';
        }
        
        if ($this->creator->avatar) {
            return $this->creator->avatar;
        }
        
        return strtoupper(substr($this->creator->f_name, 0, 1));
    }
    
    // Helper to get creator role
    public function getCreatorRoleAttribute()
    {
        if (!$this->creator) {
            return 'System';
        }
        
        $roles = [
            1 => 'Admin',
            2 => 'Registrar',
            3 => 'Teacher',
            4 => 'Student'
        ];
        
        return $roles[$this->creator->role] ?? 'User';
    }
    
    // ============ Existing Relationships ============
    
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
    
    // ============ Scopes ============
    
    // Scope for published topics
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
    
    // Scope for draft topics
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }
    
    // Scope for topics created by a specific user
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
    
    // ============ Helper Methods ============
    
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
    
    // Check if topic has PDF
    public function hasPdf()
    {
        return !empty($this->pdf_file);
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
    
    // Get PDF URL helper
    public function getPdfUrlAttribute()
    {
        if (empty($this->pdf_file)) {
            return null;
        }
        
        // If it's already a full URL or old storage path
        if (str_contains($this->pdf_file, '/storage/')) {
            // Extract just the filename
            $filename = basename($this->pdf_file);
            return asset('pdf/' . $filename);
        }
        
        // If it's just a filename (new format)
        if (!str_contains($this->pdf_file, '/')) {
            return asset('pdf/' . $this->pdf_file);
        }
        
        // If it's some other path, return as is
        return asset($this->pdf_file);
    }
}