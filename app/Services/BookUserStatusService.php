<?php

namespace App\Services;

use App\Models\Enums\BookUserStatus;
use Carbon\Carbon;

class BookUserStatusService
{

    public function __construct(
        protected BookService                 $bookService,
        protected BookUserStateService        $bookUserStateService,
        protected BookUserLogService          $bookUserLogService,
        protected BookChapterService          $bookChapterService,
        protected BookChapterUserStateService $bookChapterUserStateService,
        protected BookChapterUserLogService   $bookChapterUserLogService)
    {
    }

    public function changeBookStatus($book_id, $status)
    {
        $book = $this->bookService->findById($book_id);

        if ($book == null) abort(404);

        if (!$this->validateBookStatus($book, $status)) {
            return;
        }

        $book_user_data = [
            'book_id' => $book->id,
            'user_id' => auth()->user()->id,
            'status' => $status,
            'date' => Carbon::now()
        ];

        $this->bookUserLogService->create($book_user_data);
        $this->bookUserStateService->createOrUpdate($book_user_data);
        $this->handleBookStatusChanges($book, $status);
    }

    private function validateBookStatus($book, $status)
    {
        $book_user_status = $this->bookUserStateService->findBookUserStatus($book->id);

        if (
            ($book_user_status == null && $status == BookUserStatus::STARTED->value) ||
            ($book_user_status != null && $book_user_status == BookUserStatus::STARTED->value && $status == BookUserStatus::PAUSED->value) ||
            ($book_user_status != null && $book_user_status == BookUserStatus::STARTED->value && $status == BookUserStatus::FINISHED->value) ||
            ($book_user_status != null && $book_user_status == BookUserStatus::PAUSED->value && $status == BookUserStatus::STARTED->value) ||
            ($book_user_status != null && $book_user_status == BookUserStatus::PAUSED->value && $status == BookUserStatus::FINISHED->value)
        ) {
            return true;
        }

        return false;
    }

    private function handleBookStatusChanges($book, $status)
    {
        if ($book->book_chapters->count() == 0) return;

        if ($status == BookUserStatus::STARTED->value) {
            $book_chapter = $book->book_chapters->first(fn($chapter) => $chapter->status != BookUserStatus::FINISHED->value);
            if ($book_chapter != null) {
                $this->changeBookChapterStatus($book_chapter->id, BookUserStatus::STARTED->value);
            }
        }

        if ($status == BookUserStatus::PAUSED->value) {
            $book->book_chapters->each(function ($chapter) {
                if ($chapter->status == BookUserStatus::STARTED->value) {
                    $this->changeBookChapterStatus($chapter->id, BookUserStatus::PAUSED->value);
                }
            });
        }

        if ($status == BookUserStatus::FINISHED->value) {
            $book->book_chapters->each(function ($chapter) {
                if ($this->bookChapterUserStateService->findBookChapterUserStatus($chapter->id) == null) {
                    $this->changeBookChapterStatus($chapter->id, BookUserStatus::STARTED->value);
                }
                if ($this->bookChapterUserStateService->findBookChapterUserStatus($chapter->id) != null) {
                    $this->changeBookChapterStatus($chapter->id, BookUserStatus::FINISHED->value);
                }
            });
        }
    }

    public function changeBookChapterStatus($book_chapter_id, $status)
    {
        $book_chapter = $this->bookChapterService->findById($book_chapter_id);

        if ($book_chapter == null) abort(404);

        if (!$this->validateBookChapterStatus($book_chapter, $status)) {
            return;
        }

        $book_chapter_user_data = [
            'book_chapter_id' => $book_chapter->id,
            'user_id' => auth()->user()->id,
            'status' => $status,
            'date' => Carbon::now()
        ];

        $this->bookChapterUserLogService->create($book_chapter_user_data);
        $this->bookChapterUserStateService->createOrUpdate($book_chapter_user_data);
        $this->handleBookChapterStatusChanges($book_chapter, $status);
    }

    private function validateBookChapterStatus($book_chapter, $status)
    {
        $book_chapter_user_status = $this->bookChapterUserStateService->findBookChapterUserStatus($book_chapter->id);

        if (
            ($book_chapter_user_status == null && $status == BookUserStatus::STARTED->value) ||
            ($book_chapter_user_status != null && $book_chapter_user_status == BookUserStatus::STARTED->value && $status == BookUserStatus::PAUSED->value) ||
            ($book_chapter_user_status != null && $book_chapter_user_status == BookUserStatus::STARTED->value && $status == BookUserStatus::FINISHED->value) ||
            ($book_chapter_user_status != null && $book_chapter_user_status == BookUserStatus::PAUSED->value && $status == BookUserStatus::STARTED->value) ||
            ($book_chapter_user_status != null && $book_chapter_user_status == BookUserStatus::PAUSED->value && $status == BookUserStatus::FINISHED->value)
        ) {
            return true;
        }

        return false;
    }

    private function handleBookChapterStatusChanges($book_chapter, $status)
    {
        $book = $this->bookService->findById($book_chapter->book_id);

        if ($status == BookUserStatus::STARTED->value) {
            $this->changeBookStatus($book->id, BookUserStatus::STARTED->value);
            $book->book_chapters->each(function ($chapter) use ($book_chapter) {
                if ($chapter->id != $book_chapter->id && $chapter->status == BookUserStatus::STARTED->value) {
                    $this->changeBookChapterStatus($chapter->id, BookUserStatus::PAUSED->value);
                }
            });
        }

        if ($status == BookUserStatus::PAUSED->value) {
            if ($book->book_chapters->every(fn($chapter) => $chapter->status != BookUserStatus::STARTED->value)) {
                $this->changeBookStatus($book->id, BookUserStatus::PAUSED->value);
            }
        }

        if ($status == BookUserStatus::FINISHED->value) {
            if ($book->book_chapters->every(fn($chapter) => $chapter->status == BookUserStatus::FINISHED->value)) {
                $this->changeBookStatus($book->id, BookUserStatus::FINISHED->value);
            } else {
                $book_chapter = $book->book_chapters->first(fn($chapter) => $chapter->status != BookUserStatus::FINISHED->value);
                if ($book_chapter != null) {
                    $this->changeBookChapterStatus($book_chapter->id, BookUserStatus::STARTED->value);
                }
            }
        }
    }
}
