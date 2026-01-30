<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'type', // 'single', 'multiple', 'text'
        'points',
        'order',
        'explanation',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuizOption::class)->orderBy('order');
    }

    public function correctOptions()
    {
        return $this->hasMany(QuizOption::class)->where('is_correct', true);
    }
}