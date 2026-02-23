<?php
// app/Models/AssignmentStudentAccess.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentStudentAccess extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'assignment_student_access';

    protected $fillable = [
        'assignment_id',
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

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
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