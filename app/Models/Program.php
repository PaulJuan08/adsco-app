<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';

    protected $fillable = [
        'college_id',
        'program_name',
        'program_code',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the college that owns this program.
     */
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get the students enrolled in this program.
     */
    public function students()
    {
        return $this->hasMany(User::class, 'program_id');
    }

    /**
     * Scope a query to only include active programs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}