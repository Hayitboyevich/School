<?php

namespace App\Http\Middleware;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Quiz;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class StudentAccessMiddleware
{
    public function __construct(private Book $books, private BookChapter $bookChapters, private Quiz $quizzes)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && ($this->isStudentAuthorized($request) || $user->isAdmin(['admin', 'manager', 'teacher']))) {
            return $next($request);
        }
        abort(404);
    }

    private function isStudentAuthorized(Request $request): bool
    {
        if ($request->routeIs('books.show', 'books.change-status')) {
            return $this->isStudentInBookGroup($request);
        }

        if ($request->routeIs('book-chapter.change-status')) {
            return $this->isStudentInBookChapterGroup($request);
        }

        if ($request->routeIs('quizzes.show', 'quizzes.attempt')) {
            return $this->isStudentInQuizGroup($request);
        }

        return false;
    }

    private function isStudentInBookGroup(Request $request): bool
    {
        $bookId = $request->route()->parameter('id');
        $book = $this->books->findOrFail($bookId);

        return $this->isStudentInGroup($book->groups, $request->user());
    }

    private function isStudentInBookChapterGroup(Request $request): bool
    {
        $bookChapterId = $request->route()->parameter('id');
        $bookChapter = $this->bookChapters->findOrFail($bookChapterId);

        return $this->isStudentInGroup($bookChapter->book->groups, $request->user());
    }

    private function isStudentInQuizGroup(Request $request): bool
    {
        $user = $request->user();

        $quizId = $request->route()->parameter('id');
        $quiz = $this->quizzes->findOrFail($quizId);

        $users = $quiz->users()->pluck('users.id')->toArray();
        if (in_array($user->id, $users)) {
            return true;
        }

        return $this->isStudentInGroup($quiz->groups, $user);
//        $attempt = $quiz->attempts()->where('user_id', $user->id)->first();
//
//    if ($attempt && $attempt->status == 'started') {
//        return true; // Allow access if the student has completed the quiz
//    }
//
//    return false;
    }

    private function isStudentInGroup(Collection $groups, User $user): bool
    {
        $group_ids = $groups->pluck('id')->toArray();
        return $user->groups()->whereIn('group_id', $group_ids)->exists();
    }
}
