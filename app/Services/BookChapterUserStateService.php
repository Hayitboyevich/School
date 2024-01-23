<?php

namespace App\Services;

use App\Models\BookChapterUserState;

class BookChapterUserStateService
{
    public function __construct(
        protected BookChapterUserState $book_chapter_user_states)
    {
    }

    public function findBookChapterUserStatus($book_chapter_id, $user_id = null)
    {
        if ($user_id == null) $user_id = auth()->user()->id;
        return $this->book_chapter_user_states
            ->where('book_chapter_id', $book_chapter_id)
            ->where('user_id', $user_id)
            ->first()
            ->status ?? null;
    }

    public function createOrUpdate($book_chapter_user_data)
    {
        $book_chapter_user_state = $this->book_chapter_user_states
            ->where('book_chapter_id', $book_chapter_user_data['book_chapter_id'])
            ->where('user_id', $book_chapter_user_data['user_id'])
            ->first();

        if ($book_chapter_user_state == null) {
            $this->book_chapter_user_states->create($book_chapter_user_data);
        } else {
            $book_chapter_user_state->update($book_chapter_user_data);
        }
    }
}
