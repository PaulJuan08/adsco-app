<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseDiscussion extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'user_id', 'parent_id', 'body'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(CourseDiscussion::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(CourseDiscussion::class, 'parent_id')
            ->with('author:id,f_name,l_name,role,sex')
            ->oldest();
    }
}
