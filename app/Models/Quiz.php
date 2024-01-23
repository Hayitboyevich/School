<?php

namespace App\Models;

use App\Models\Enums\QuizAccess;
use App\Models\Enums\QuizQuestionOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $casts = [
        'group_level' => 'array',
        'access' => QuizAccess::class,
        'question_order' => QuizQuestionOrder::class
    ];

    protected $fillable = [
        'name',
        'description',
        'group_level',
        'status',
        'end_date',
        'link',
        'access',
        'end_date',
        'question_order',
        'purpose',
        'duration',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class);
    }

    public function academic_years()
    {
        return $this->belongsToMany(AcademicYear::class);
    }

    public function feedback()
    {
        return $this->morphMany(Feedback::class, 'record');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    public function book_chapters()
    {
        return $this->belongsToMany(BookChapter::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function authors()
    {
        return $this->belongsToMany(User::class, 'author_quiz');
    }

    public function test_type()
    {
        return $this->belongsTo(TestType::class);
    }
}
