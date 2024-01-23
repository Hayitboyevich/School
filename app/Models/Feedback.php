<?php

namespace App\Models;

use App\Models\Enums\FeedbackType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'record_id',
        'content',
    ];

    protected $casts = [
        'type' => FeedbackType::class
    ];

    public function record()
    {
        return $this->morphTo(__FUNCTION__, 'type', 'record_id');
    }
}
