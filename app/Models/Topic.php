<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $table = 'topics';
    
    protected $fillable = [
        'title',
        'order',
        'attachment',
        'video_link', 
        'is_published',
        'learning_outcomes',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}