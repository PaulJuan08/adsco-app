<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_name',
        'college_year',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the programs for this college.
     */
    public function programs()
    {
        return $this->hasMany(Program::class, 'college_id');
    }

    /**
     * Alias for programs() to maintain backward compatibility
     */
    public function collegeCourses()
    {
        return $this->programs();
    }

    /**
     * Get the students enrolled in this college.
     */
    public function students()
    {
        return $this->hasMany(User::class, 'college_id')->where('role', 4);
    }

    /**
     * Scope a query to only include active colleges.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}