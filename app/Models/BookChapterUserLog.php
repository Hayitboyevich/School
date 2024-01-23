<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookChapterUserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_chapter_id',
        'user_id',
        'status',
        'date',
    ];
}
