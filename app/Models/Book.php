<?php

namespace App\Models;

use App\Models\Enums\BookUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cover',
        'description',
        'page_count',
        'file_url',
        'reference_link',
        'group_level',
        'start_date',
        'end_date',
        'score'
    ];

    protected $casts = [
        'group_level' => 'array'
    ];

    public function coverUrl()
    {
        return $this->cover ? Storage::disk('public')->url($this->cover) : $this->getDefaultCover();
    }

    public function getDefaultCover()
    {
        return '/placeholders/no-book-cover.png';
    }

    public function fileUrl()
    {
        return $this->file_url ? Storage::disk('public')->url($this->file_url) : null;
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function academic_years()
    {
        return $this->belongsToMany(AcademicYear::class);
    }

    public function book_authors()
    {
        return $this->belongsToMany(BookAuthor::class);
    }

    public function book_chapters()
    {
        return $this->hasMany(BookChapter::class);
    }

    public function feedback()
    {
        return $this->morphMany(Feedback::class, 'record');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_user_states')->withPivot('date', 'status')->withTimestamps();
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }

    public function logs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_user_logs')->withPivot('date', 'status')->withTimestamps();
    }

    public static function getBookUser($bookId)
    {
        $user = Auth::user();
        $bookUser = BookUserStatus::where('book_id', $bookId)->where('user_id', $user->id)->first();
         return $bookUser->status ?? null;
    }

    public function getPercentAttribute($value)
    {
        return $value . '%';
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('start_date','end_date');
    }
}
