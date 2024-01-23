<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Enums\BookUserStatus;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function __construct(
        protected Book                        $books,
        protected BookUserStateService        $bookUserStateService,
        protected BookUserLogService          $bookUserLogService,
        protected BookChapterUserStateService $bookChapterUserStateService,
        protected BookChapterUserLogService   $bookChapterUserLogService,
        protected BookChapterService          $bookChapterService
    )
    {
    }

    public function findAllPaginatedWithStatus($status = null, $page = 12)
    {
        $books = $this->books
            ->select('books.*')
            ->when($status != null, function ($query) use ($status) {
                $query
                    ->addSelect('book_user_states.status')
                    ->leftJoin('book_user_states', function ($join) {
                        $join->on('book_id', '=', 'books.id')
                            ->on('user_id', '=', DB::raw(auth()->user()->id));
                    })
                    ->where('book_user_states.status', $status);
            })
            ->when(auth()->user() && auth()->user()->isStudent(), function ($query) {
                $query->whereHas('groups', function ($subQuery) {
                    $subQuery->whereHas('users', function ($subInQuery) {
                        $subInQuery->where('users.id', auth()->user()->id);
                    });
                });
            })
            ->orderBy('start_date')
            ->paginate($page);

        $books->map(function ($book) {
            $book->status = $this->bookUserStateService->findBookUserStatus($book->id);
            return $book;
        });

        return $books;
    }

    public function findById($id)
    {
        $book = $this->books
            ->with(['book_chapters' => fn($q) => $q->orderBy('order')])
            ->find($id);

        if ($book == null) return null;

        $book->status = $this->bookUserStateService->findBookUserStatus($book->id);
        $book->started = $this->bookUserLogService->findBookUserStarted($book->id);
        $book->finished = $this->bookUserLogService->findBookUserFinished($book->id);

        $book->book_chapters->map(function ($book_chapter) {
            $book_chapter->status = $this->bookChapterUserStateService->findBookChapterUserStatus($book_chapter->id);
            $book_chapter->started = $this->bookChapterUserLogService->findBookChapterUserStarted($book_chapter->id);
            $book_chapter->finished = $this->bookChapterUserLogService->findBookChapterUserFinished($book_chapter->id);
            return $book_chapter;
        });

        return $book;
    }

    public function getGroupedBookChapterOptions($book_ids)
    {
        $options = [];
        foreach ($book_ids as $book_id) {
            $book = $this->findById($book_id);
            $book_chapters = $book->book_chapters->pluck('name', 'id')->toArray();
            $options[$book->name] = $book_chapters;
        }
        return $options;
    }

    public function calculateProgress($book)
    {
        if ($book->status == null) {
            return 0;
        }

        if ($book->book_chapters->count() == 0) {
            return 100;
        }

        if ($this->anyPageCountIsNull($book)) {
            return $this->calculateProgressByChapterCount($book);
        } else {
            return $this->calculateProgressByPageCount($book);
        }
    }

    private function anyPageCountIsNull($book)
    {
        if ($book->page_count === null || $book->page_count === 0) {
            return true;
        }

        foreach ($book->book_chapters as $book_chapter) {
            if ($book_chapter->page_start === null || $book_chapter->page_end === null) {
                return true;
            }
        }

        return false;
    }

    public function calculateProgressByChapterCount($book)
    {
        return ceil($book->book_chapters->percentage(fn($chapter) => $chapter->status == BookUserStatus::FINISHED->value));
    }

    public function calculateProgressByPageCount($book)
    {
        $finishedPagesCount = 0;

        foreach ($book->book_chapters as $book_chapter) {
            if ($book_chapter->status == BookUserStatus::FINISHED->value) {
                $finishedPagesCount += $book_chapter->page_end - $book_chapter->page_start + 1;
            }
        }
        return ceil($finishedPagesCount * 100 / $book->page_count);
    }
}
