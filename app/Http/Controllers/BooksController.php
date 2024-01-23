<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use App\Services\BookUserStatusService;

class BooksController
{

    private $bookService;
    private $bookUserStatusService;

    public function __construct(BookService           $bookService,
                                BookUserStatusService $bookUserStatusService)
    {
        $this->bookService = $bookService;
        $this->bookUserStatusService = $bookUserStatusService;
    }

    public function index()
    {
        $status = request()->query('status');
        $books = $this->bookService->findAllPaginatedWithStatus($status);
        return view('books.index', compact('books'));
    }

    public function show($id)
    {
        $book = $this->bookService->findById($id);
        $progress = $this->bookService->calculateProgress($book);

        if ($book == null) abort(404);

        return view('books.show', compact('book', 'progress'));
    }

    public function change_status($id, $status)
    {
        $this->bookUserStatusService->changeBookStatus($id, $status);
        return redirect()->back();
    }
}
