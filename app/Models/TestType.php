<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestType extends Model
{
    use HasFactory;

    protected $fillable = [
        'label'
    ];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
