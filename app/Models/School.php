<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'number',
        'name',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
