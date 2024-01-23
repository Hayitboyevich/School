<?php

namespace App\Http\Controllers;

use App\Models\Enums\AttemptStatus;
use App\Services\AttemptService;
use App\Services\QuizService;

class QuizzesController extends Controller
{
    private $quizService;
    private $attemptService;

    public function __construct(QuizService $quizService, AttemptService $attemptService)
    {
        $this->quizService = $quizService;
        $this->attemptService = $attemptService;
    }

    public function index()
    {
        $quizzes = $this->quizService->findAllPaginatedWithStatus();
        return view('quizzes.index', compact('quizzes'));
    }

    public function show($id)
    {
        $user_id = auth()->user()->id;
        $quiz = $this->quizService->findByIdOrLink($id);
        if ($quiz == null) {
            abort(404);
        }
        if ($quiz->link == $id) {
            return redirect(route('quizzes.show', $quiz->id));
        }
        $attempts = $this->attemptService->getByUserId($quiz->id, $user_id);
        $attempt = $attempts->first();
        return view('quizzes.show', compact('quiz', 'attempts', 'attempt'));
    }
        public function result($id)
       {
            $user_id = auth()->user()->id;
            $quiz = $this->quizService->findByIdOrLink($id);
            if ($quiz == null) {
                abort(404);
            }
               if ($quiz->link == $id) {
            return redirect(route('quizzes.result', $quiz->id));
        }
               $attempts = $this->attemptService->getByUserId($quiz->id, $user_id);
            $attempt = $attempts->first();
            return view('quizzes.shows', compact('quiz', 'attempts', 'attempt'));
       }

    public function attempt($id)
    {
        $attempt = $this->attemptService->attempt($id);
        $attempt = $this->attemptService->handleChoices($attempt);
        if ($attempt->status == AttemptStatus::STARTED) {
            return view('quizzes.attempt', compact('attempt'));
        } else {
            return view('quizzes.result', compact('attempt'));
        }
    }
}
