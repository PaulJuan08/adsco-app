<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'topic_id',
        'status',
        'completed_at',
        'notes',
        'time_spent'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'time_spent' => 'integer'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // Get course through topic (handles both direct and pivot relationships)
    public function course()
    {
        if ($this->topic) {
            // Check if topic has direct course relationship
            if ($this->topic->course) {
                return $this->topic->course;
            }
            
            // Check if topic has courses through pivot
            if ($this->topic->courses && $this->topic->courses->isNotEmpty()) {
                return $this->topic->courses->first();
            }
        }
        
        return null;
    }

    // Add a scope for completed progress
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Add a scope for in progress
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Check if progress exists for a student and topic
    public static function checkStatus($studentId, $topicId)
    {
        return self::where('student_id', $studentId)
            ->where('topic_id', $topicId)
            ->first();
    }

    // Mark as complete
    public static function markComplete($studentId, $topicId, $notes = '')
    {
        return self::updateOrCreate(
            [
                'student_id' => $studentId,
                'topic_id' => $topicId
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $notes
            ]
        );
    }

    // Mark as incomplete
    public static function markIncomplete($studentId, $topicId)
    {
        return self::where('student_id', $studentId)
            ->where('topic_id', $topicId)
            ->update([
                'status' => 'incomplete',
                'completed_at' => null
            ]);
    }
}