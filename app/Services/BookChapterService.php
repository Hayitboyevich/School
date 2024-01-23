<?php

namespace App\Services;

use App\Models\BookChapter;

class BookChapterService
{

    public function __construct(
        protected BookChapter                 $book_chapters,
        protected BookChapterUserStateService $bookChapterUserStateService,
        protected BookChapterUserLogService   $bookChapterUserLogService
    )
    {
    }

    public function findById($id)
    {
        $book_chapter = $this->book_chapters->find($id);

        if ($book_chapter == null) return null;

        $book_chapter->status = $this->bookChapterUserStateService->findBookChapterUserStatus($book_chapter->id);
        $book_chapter->started = $this->bookChapterUserLogService->findBookChapterUserStarted($book_chapter->id);
        $book_chapter->finished = $this->bookChapterUserLogService->findBookChapterUserFinished($book_chapter->id);

        return $book_chapter;
    }

    public function filterOut($bookIds, $bookChapterIds)
    {
        return $this->book_chapters
            ->whereIn('id', $bookChapterIds)
            ->whereIn('book_id', $bookIds)
            ->get()
            ->pluck('id')
            ->toArray();
    }

    public function associateGroups($chapters, $groups)
    {
        foreach ($groups as $group) {
            $group->book_chapters()->sync($chapters->pluck('id'));
        }
    }

    public function disassociateGroups($chapters, $groups)
    {
        foreach ($groups as $group) {
            $group->book_chapters()->detach($chapters->pluck('id'));
        }
    }
}
