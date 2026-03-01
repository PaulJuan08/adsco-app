<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Encryptable;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Storage; // Add this

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Encryptable;

    protected $fillable = [
        'f_name',
        'l_name',
        'email',
        'password',
        'age',
        'sex',
        'contact',
        'role',
        'employee_id',
        'student_id',
        'is_approved',
        'approved_at',
        'approved_by',
        'created_by',
        'last_login_at',
        'college_id',
        'program_id',
        'college_year',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved'       => 'boolean',
        'approved_at'       => 'datetime',
        'last_login_at'     => 'datetime',
    ];

    protected $appends = [
        'full_name',
        'role_name',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function collegeCourse()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function coursesAsTeacher()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
            ->withPivot('enrolled_at', 'status', 'grade')
            ->withTimestamps();
    }

    // REMOVE attendances relationship since you don't use it

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'user_id');
    }

    public function quizAccess()
    {
        return $this->hasMany(QuizStudentAccess::class, 'student_id');
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    public function assignmentAccess()
    {
        return $this->hasMany(AssignmentStudentAccess::class, 'student_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completedTopics()
    {
        return $this->belongsToMany(Topic::class, 'progress', 'student_id', 'topic_id')
            ->wherePivot('status', 'completed')
            ->withPivot('completed_at', 'notes')
            ->withTimestamps();
    }

    public function progress()
    {
        return $this->hasMany(Progress::class, 'student_id');
    }

    // ── Model Events for Cleanup ─────────────────────────────────────────────

    protected static function booted()
    {
        static::deleting(function ($user) {
            // Only handle file cleanup for assignment submissions
            // Database will handle record deletions via foreign keys if set
            if ($user->role == 4) { // Student
                foreach ($user->assignmentSubmissions as $submission) {
                    if ($submission->attachment_path && 
                        Storage::disk('public')->exists($submission->attachment_path)) {
                        Storage::disk('public')->delete($submission->attachment_path);
                    }
                }
            }
            
            // Log the deletion
            \Log::info('User being deleted with cleanup', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);
        });
    }

    // ── Role helpers ──────────────────────────────────────────────────────────

    public function isAdmin():     bool { return $this->role == 1; }
    public function isRegistrar(): bool { return $this->role == 2; }
    public function isTeacher():   bool { return $this->role == 3; }
    public function isStudent():   bool { return $this->role == 4; }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim($this->f_name . ' ' . $this->l_name);
    }

    public function getRoleNameAttribute(): string
    {
        return [1 => 'Admin', 2 => 'Registrar', 3 => 'Teacher', 4 => 'Student'][$this->role] ?? 'Unknown';
    }

    public function getIdentifierAttribute(): string
    {
        if ($this->isStudent())                          return $this->student_id  ?? $this->email;
        if ($this->isTeacher() || $this->isRegistrar()) return $this->employee_id ?? $this->email;
        return $this->email;
    }

    public function getGpaAttribute(): ?float
    {
        if (!$this->isStudent()) return null;
        $grades = $this->enrollments()->whereNotNull('grade')->get();
        return $grades->isEmpty() ? 0.0 : round($grades->avg('grade'), 2);
    }

    public function getCompletedCoursesCountAttribute(): int
    {
        return $this->isStudent() ? $this->enrollments()->whereNotNull('grade')->count() : 0;
    }

    public function getActiveCoursesCountAttribute(): int
    {
        return $this->isStudent() ? $this->enrollments()->whereNull('grade')->count() : 0;
    }

    public function getFormattedContactAttribute(): ?string
    {
        if (!$this->contact) return null;
        $c = preg_replace('/\D/', '', $this->contact);
        if (strlen($c) === 10) return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $c);
        if (strlen($c) === 11) return preg_replace('/(\d{1})(\d{3})(\d{3})(\d{4})/', '+$1 ($2) $3-$4', $c);
        return $this->contact;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeApproved($query)      { return $query->where('is_approved', true); }
    public function scopePending($query)        { return $query->where('is_approved', false); }
    public function scopeByRole($query, $role)  { return $query->where('role', $role); }
    public function scopeVerified($query)       { return $query->whereNotNull('email_verified_at'); }
    public function scopeUnverified($query)     { return $query->whereNull('email_verified_at'); }

    // ── Email Verification ───────────────────────────────────────────────────

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail());
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function isApproved(): bool { return $this->is_approved === true; }

    public function recordLogin(): void { $this->update(['last_login_at' => now()]); }

    public function approve(): void
    {
        $this->update(['is_approved' => true, 'approved_at' => now()]);
    }
}