<?php

namespace App\Models;

use App\Models\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_type_id',
        'content',
        'type',
        'group_level',
        'time',
        'purpose'
    ];

    protected $casts = [
        'group_level' => 'array',
        'type' => QuestionType::class
    ];

    public function getContentAttribute()
    {
        return $this->question_contents->first() ? $this->question_contents->first()->value : '';
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function question_contents()
    {
        return $this->hasMany(QuestionContent::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }

    public function attempt_questions()
    {
        return $this->hasMany(AttemptQuestion::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    public function authors()
    {
        return $this->belongsToMany(User::class, 'author_question');
    }
}
