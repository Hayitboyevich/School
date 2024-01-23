<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookChapterUserState extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_chapter_id',
        'user_id',
        'status',
        'date',
    ];

    public function book_chapter()
    {
        return $this->belongsTo(BookChapter::class);
    }

     public function user()
        {
            return $this->belongsTo(User::class);
        }
}
