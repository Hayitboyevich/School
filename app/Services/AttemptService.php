<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\AttemptQuestion;
use App\Models\Enums\AttemptStatus;
use App\Models\Enums\QuizQuestionOrder;
use App\Models\Quiz;
use Carbon\Carbon;

class AttemptService
{
    public function __construct(
        protected Quiz            $quizzes,
        protected Attempt         $attempts,
        protected AttemptQuestion $attempt_questions,
        protected AttemptAnswer   $attempt_answers)
    {
    }

    public function attempt($quiz_id)
    {
        $quiz = $this->quizzes->findOrFail($quiz_id);

        if ($quiz->questions->isEmpty()) {
            abort(404);
        }

        $attempt = $this->attempts
            ->where('quiz_id', $quiz_id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($attempt != null) return $attempt;

        $attempt = $this->create($quiz);
        $this->createQuestions($attempt);
        $attempt->update([
            'attempt_question_id' => $attempt->attempt_questions->first()->id,
            'attempt_question_order' => 1
        ]);
        return $attempt;
    }

    public function create($quiz)
    {
        return $this->attempts->create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->user()->id,
            'status' => AttemptStatus::STARTED,
            'start_date' => Carbon::now(),
            'score' => 0
        ]);
    }

    private function createQuestions($attempt)
    {
        $questions = $attempt->quiz->questions;

        if ($attempt->quiz->question_order == QuizQuestionOrder::RANDOM) {
            $questions = $questions->shuffle();
        }

        $questions->each(function ($question) use ($attempt) {
            $attempt_question = $this->attempt_questions->create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'content' => $question->question_contents->random()->value
            ]);

            $question->answers->each(function ($answer) use ($attempt_question) {
                $this->attempt_answers->create([
                    'attempt_question_id' => $attempt_question->id,
                    'content' => $answer->content,
                    'is_selected' => false,
                    'is_correct' => $answer->is_correct,
                    'score' => $answer->score
                ]);
            });
        });
    }

    public function handleChoices($attempt)
    {
        $this->handleAnswers();
        $this->handleDirections($attempt);
        $this->handleFinish($attempt);
        return $attempt;
    }

    private function handleAnswers()
    {
        $answer = request()->input('answer');
        if ($answer != null) {
            $attempt_answer = $this->attempt_answers->find($answer);
            $attempt_answer->attempt_question->attempt_answers->each(function ($attempt_answer) {
                $attempt_answer->update(['is_selected' => false]);
            });
            $attempt_answer->update([
                'is_selected' => true
            ]);
        }
    }

    private function handleDirections($attempt)
    {
        $attempt_question_order = $attempt->attempt_question_order;
        if (request()->input('direction') == 'next') {
            $attempt_question_order = min($attempt->attempt_question_order + 1, $attempt->attempt_questions->count());
        }
        if (request()->input('direction') == 'prev') {
            $attempt_question_order = max(1, $attempt->attempt_question_order - 1);
        }
        $attempt_question = $attempt->attempt_questions[$attempt_question_order - 1];
        $attempt->update([
            'attempt_question_id' => $attempt_question->id,
            'attempt_question_order' => $attempt_question_order
        ]);
    }

    private function handleFinish($attempt)
    {
        if (request()->input('direction') != 'finish') {
            return;
        }

        $score = 0;
        $all = $attempt->attempt_questions->count();
        $correct = 0;
        $attempt->attempt_questions->each(function ($question) use (&$score, &$correct) {
            $tmp = $score;
            $question->attempt_answers->each(function ($answer) use (&$score) {
                if ($answer->is_selected && $answer->is_correct) {
                    $score += $answer->score;
                }
            });
            if ($tmp != $score) $correct++;
        });

        $percentage = ($correct / $all) * 100;

        if ($percentage < 56) {
            $finalScore = 2;
        } elseif ($percentage < 71) {
            $finalScore = 3;
        } elseif ($percentage < 86) {
            $finalScore = 4;
        } else {
            $finalScore = 5;
        }

        $attempt->update([
            'score' => $finalScore,
            'status' => AttemptStatus::FINISHED,
            'correct' => $correct,
            'incorrect' => $all - $correct,
            'all' => $all,
            'end_date' => Carbon::now()
        ]);
    }

    public function getByUserId($quiz_id, $user_id)
    {
        return $this->attempts
            ->where('quiz_id', $quiz_id)
            ->where('user_id', $user_id)
            ->get();
    }
}
