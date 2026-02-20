<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Encryptable;

class User extends Authenticatable
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
        // ── Academic fields (students only) ──────────────────
        'college_id',           // which college (FK → colleges.id)
        'program_id',           // which degree program (FK → programs.id) - CHANGED FROM college_course_id
        'college_year',         // year level string e.g. "1st Year"
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

    /** The college this student belongs to. */
    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    /**
     * The degree program this student is enrolled in.
     * e.g. "BS Civil Engineering" from the programs table.
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Alias for program() to maintain backward compatibility
     * if any old code still calls collegeCourse()
     */
    public function collegeCourse()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /** Subjects/courses taught as a teacher. */
    public function coursesAsTeacher()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /** Subject enrollments (the courses/subjects table). */
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

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'user_id');
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

    // ── Actions ───────────────────────────────────────────────────────────────

    public function isApproved(): bool { return $this->is_approved === true; }

    public function recordLogin(): void { $this->update(['last_login_at' => now()]); }

    public function approve(): void
    {
        $this->update(['is_approved' => true, 'approved_at' => now()]);
    }
}