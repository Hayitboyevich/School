<?php

namespace App\Models;

use App\Models\Enums\SubjectType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'direction_id',
        'external_id'
    ];

    protected $casts = [
        'type' => SubjectType::class
    ];

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
