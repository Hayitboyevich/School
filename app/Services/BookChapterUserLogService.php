<?php

namespace App\Services;

use App\Models\BookChapterUserLog;
use App\Models\Enums\BookUserStatus;

class BookChapterUserLogService
{
    public function __construct(
        protected BookChapterUserLog $book_chapter_user_logs
    )
    {
    }

    public function findBookChapterUserStarted($book_chapter_id)
    {
        return $this->book_chapter_user_logs
            ->where('book_chapter_id', $book_chapter_id)
            ->where('user_id', auth()->user()->id)
            ->where('status', BookUserStatus::STARTED)
            ->orderBy('date')
            ->first()
            ->date ?? null;
    }

    public function findBookChapterUserFinished($book_chapter_id, $user_id = null)
    {
        if ($user_id == null) $user_id = auth()->user()->id;
        return $this->book_chapter_user_logs
            ->where('book_chapter_id', $book_chapter_id)
            ->where('user_id', $user_id)
            ->where('status', BookUserStatus::FINISHED)
            ->orderBy('date')
            ->first()
            ->date ?? null;
    }

    public function create($book_chapter_user_data)
    {
        $this->book_chapter_user_logs->create($book_chapter_user_data);
    }
}
