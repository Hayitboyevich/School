<?php

namespace App\Services;

use App\Models\BookUserState;

class BookUserStateService
{
    public function __construct(
        protected BookUserState $book_user_states
    )
    {
    }

    public function findBookUserStatus($book_id, $user_id = null)
    {
        if ($user_id == null) $user_id = auth()->user()->id;
        return $this->book_user_states
            ->where('book_id', $book_id)
            ->where('user_id', $user_id)
            ->first()
            ->status ?? null;
    }

    public function createOrUpdate($book_user_data)
    {
        $book_user_state = $this->book_user_states
            ->where('book_id', $book_user_data['book_id'])
            ->where('user_id', $book_user_data['user_id'])
            ->first();

        if ($book_user_state == null) {
            $this->book_user_states->create($book_user_data);
        } else {
            $book_user_state->update($book_user_data);
        }
    }
}
