<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class BookChapter extends Model implements Sortable
{
    use SortableTrait;
    use HasFactory;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'name',
        'page_start',
        'page_end',
        'book_id',
        'start_date',
        'end_date',
        'score',
        'order'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function feedback()
    {
        return $this->morphMany(Feedback::class, 'record');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }

    public function group()
    {
        return $this->belongsToMany(Group::class);
    }

    public function book_chapter_user_states()
    {
        return $this->hasMany(BookChapterUserState::class);
    }
}
