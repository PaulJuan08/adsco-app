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
        'is_approved'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'last_login_at' => 'datetime'
    ];
    
    protected $appends = [
        'full_name',
        'role_name'
    ];
    
    // Relationships
    public function coursesAsTeacher()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }
    
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    /**
     * Get the audit logs for the user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the courses taught by the user (if teacher).
     */
    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /**
     * Get the courses enrolled by the user (if student).
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
            ->withPivot('enrolled_at', 'status', 'grade')
            ->withTimestamps();
    }
    
    /**
     * Get quiz attempts for the user
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'user_id');
    }
    
    /**
     * Get assignments submitted by the user
     */
    // public function submittedAssignments()
    // {
    //     return $this->hasMany(AssignmentSubmission::class, 'student_id');
    // }
    
    /**
     * Get grades for the user
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    /**
     * Get the user who approved this user
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who created this user
     */
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
    
    // Helper Methods
    public function isAdmin()
    {
        return $this->role == 1;
    }
    
    public function isRegistrar()
    {
        return $this->role == 2;
    }
    
    public function isTeacher()
    {
        return $this->role == 3;
    }
    
    public function isStudent()
    {
        return $this->role == 4;
    }
    
    public function getFullNameAttribute()
    {
        return trim($this->f_name . ' ' . $this->l_name);
    }
    
    public function getRoleNameAttribute()
    {
        $roles = [
            1 => 'Admin',
            2 => 'Registrar',
            3 => 'Teacher',
            4 => 'Student'
        ];
        
        return $roles[$this->role] ?? 'Unknown';
    }
    
    /**
     * Check if user is approved
     */
    public function isApproved()
    {
        return $this->is_approved === true;
    }
    
    /**
     * Get the user's ID based on their role
     */
    public function getIdentifierAttribute()
    {
        if ($this->isStudent()) {
            return $this->student_id;
        } elseif ($this->isTeacher() || $this->isRegistrar()) {
            return $this->employee_id;
        }
        
        return $this->email;
    }
    
    /**
     * Get student's current GPA
     */
    public function getGpaAttribute()
    {
        if (!$this->isStudent()) {
            return null;
        }
        
        $grades = $this->enrollments()
            ->whereNotNull('grade')
            ->get();
        
        if ($grades->isEmpty()) {
            return 0.0;
        }
        
        return round($grades->avg('grade'), 2);
    }
    
    /**
     * Get student's completed courses count
     */
    public function getCompletedCoursesCountAttribute()
    {
        if (!$this->isStudent()) {
            return 0;
        }
        
        return $this->enrollments()
            ->whereNotNull('grade')
            ->count();
    }
    
    /**
     * Get student's active courses count
     */
    public function getActiveCoursesCountAttribute()
    {
        if (!$this->isStudent()) {
            return 0;
        }
        
        return $this->enrollments()
            ->whereNull('grade')
            ->count();
    }
    
    /**
     * Scope for approved users
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
    
    /**
     * Scope for pending approval
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }
    
    /**
     * Scope by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
    
    /**
     * Get formatted contact number
     */
    public function getFormattedContactAttribute()
    {
        if (!$this->contact) {
            return null;
        }
        
        // Remove any non-digit characters
        $contact = preg_replace('/\D/', '', $this->contact);
        
        // Format based on length
        if (strlen($contact) === 10) {
            return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $contact);
        } elseif (strlen($contact) === 11) {
            return preg_replace('/(\d{1})(\d{3})(\d{3})(\d{4})/', '+$1 ($2) $3-$4', $contact);
        }
        
        return $this->contact;
    }
    
    /**
     * Record login timestamp
     */
    public function recordLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
    
    /**
     * Approve the user
     */
    public function approve()
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now()
        ]);
    }

}