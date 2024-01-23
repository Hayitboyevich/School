<?php

namespace App\Http\Controllers;

use App\Services\BookUserStatusService;

class BookChapterController
{
    private $bookUserStatusService;

    public function __construct(BookUserStatusService $bookUserStatusService)
    {
        $this->bookUserStatusService = $bookUserStatusService;
    }

    public function change_status($id, $status)
    {
        $this->bookUserStatusService->changeBookChapterStatus($id, $status);
        return redirect()->back();
    }
}

