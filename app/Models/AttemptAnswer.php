<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_question_id',
        'content',
        'is_selected',
        'is_correct',
        'score',
    ];

    public function attempt_question()
    {
        return $this->belongsTo(AttemptQuestion::class);
    }
}
