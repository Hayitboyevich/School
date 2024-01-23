<?php

namespace App\Models;

use App\Models\Enums\AttemptStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_question_id',
        'attempt_question_order',
        'score',
        'correct',
        'incorrect',
        'all',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'status' => AttemptStatus::class,
    ];
    
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attempt_questions()
    {
        return $this->hasMany(AttemptQuestion::class);
    }

    public function attempt_question()
    {
        return $this->belongsTo(AttemptQuestion::class);
    }
}
