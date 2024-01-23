<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use App\Services\BookUserStatusService;
use App\Services\QuizService;

class DashboardController extends Controller
{
    public function __construct(
        protected QuizService           $quizService,
        protected BookService           $bookService,
        protected BookUserStatusService $bookUserStatusService)
    {
    }

    public function index()
    {
        $quizzes = $this->quizService->findAllPaginatedWithStatus();
        $status = request()->query('status');
        $books = $this->bookService->findAllPaginatedWithStatus($status);
        return view('home', compact('quizzes','books'));
    }
}
