<?php
// app/Models/QuizStudentAccess.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizStudentAccess extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'quiz_student_access';

    protected $fillable = [
        'quiz_id',
        'student_id',
        'status',
        'granted_by',
        'granted_at',
        'expires_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function granter()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}