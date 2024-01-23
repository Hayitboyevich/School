<?php

namespace App\Services;

use App\Models\BookUserLog;
use App\Models\Enums\BookUserStatus;

class BookUserLogService
{
    public function __construct(
        protected BookUserLog $book_user_logs
    )
    {
    }

    public function findBookUserStarted($book_id, $user_id = null)
    {
        if ($user_id == null) $user_id = auth()->user()->id;
        return $this->book_user_logs
            ->where('book_id', $book_id)
            ->where('user_id', $user_id)
            ->where('status', BookUserStatus::STARTED)
            ->orderBy('date')
            ->first()
            ->date ?? null;
    }

    public function findBookUserFinished($book_id, $user_id = null)
    {
        if ($user_id == null) $user_id = auth()->user()->id;
        return $this->book_user_logs
            ->where('book_id', $book_id)
            ->where('user_id', $user_id)
            ->where('status', BookUserStatus::FINISHED)
            ->orderBy('date')
            ->first()
            ->date ?? null;
    }

    public function create($book_user_data)
    {
        $this->book_user_logs->create($book_user_data);
    }
}
