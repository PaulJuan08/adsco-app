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
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'points' => 'integer',
        'is_published' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}