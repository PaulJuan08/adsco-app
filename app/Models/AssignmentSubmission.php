<?php
// app/Models/AssignmentSubmission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'assignment_submissions';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'answer_text',
        'attachment_path',
        'status',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'submitted_at',
    ];

    protected $casts = [
        'graded_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getScorePercentageAttribute(): float
    {
        if (!$this->assignment || !$this->assignment->points || $this->score === null) {
            return 0;
        }
        return round(($this->score / $this->assignment->points) * 100, 1);
    }
}