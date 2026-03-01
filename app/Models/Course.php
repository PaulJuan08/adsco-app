<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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
        'max_students',
        'created_by' // ADD THIS - needed for the creator relationship
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

    // ADD THIS RELATIONSHIP - for the user who created the course
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with topics through pivot table (ORDERED)
    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'course_topics')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('course_topics.order');
    }

    // Relationship with enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

    // Relationship with students
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->withTimestamps();
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

    // Get formatted credits
    public function getFormattedCreditsAttribute()
    {
        return $this->credits . ' ' . ($this->credits == 1 ? 'Credit' : 'Credits');
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
        $lastTopic = $this->topics()->orderByDesc('course_topics.order')->first();
        return $lastTopic ? ($lastTopic->pivot->order + 1) : 1;
    }

    // Get enrolled students count
    public function getEnrolledStudentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

    // Calculate course progress for a specific student
    public function getStudentProgress($studentId)
    {
        $topics = $this->topics;
        
        if ($topics->isEmpty()) {
            return [
                'total' => 0,
                'completed' => 0,
                'percentage' => 0
            ];
        }
        
        $completedTopicIds = DB::table('progress')
            ->where('student_id', $studentId)
            ->whereIn('topic_id', $topics->pluck('id'))
            ->where('status', 'completed')
            ->pluck('topic_id')
            ->toArray();
        
        $totalTopics = $topics->count();
        $completedTopics = count($completedTopicIds);
        $progressPercentage = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;
        
        return [
            'total' => $totalTopics,
            'completed' => $completedTopics,
            'percentage' => $progressPercentage
        ];
    }

    // Check if student is enrolled
    public function isEnrolled($studentId)
    {
        return $this->enrollments()->where('student_id', $studentId)->exists();
    }

    // Get student enrollment
    public function getStudentEnrollment($studentId)
    {
        return $this->enrollments()->where('student_id', $studentId)->first();
    }

    // Add topic to course with order
    public function addTopicWithOrder($topicId, $order = null)
    {
        if ($order === null) {
            $order = $this->next_topic_order;
        }
        
        $this->topics()->attach($topicId, ['order' => $order]);
        
        return $this;
    }

    // Remove topic from course
    public function removeTopic($topicId)
    {
        $this->topics()->detach($topicId);
        
        // Reorder remaining topics
        $this->reorderTopics();
        
        return $this;
    }

    // Reorder topics sequentially
    public function reorderTopics()
    {
        $topics = $this->topics()->orderBy('course_topics.order')->get();
        
        $order = 1;
        foreach ($topics as $topic) {
            DB::table('course_topics')
                ->where('course_id', $this->id)
                ->where('topic_id', $topic->id)
                ->update(['order' => $order]);
            $order++;
        }
        
        return $this;
    }
}