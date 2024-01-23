<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'question_type_id',
        'content',
    ];

    public function attempt()
    {
        return $this->belongsTo(Attempt::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function attempt_answers()
    {
        return $this->hasMany(AttemptAnswer::class);
    }
}
