<?php

namespace App\Services;

use App\Models\Enums\QuizAccess;
use App\Models\Quiz;
use Illuminate\Support\Str;

class QuizService
{
    public function __construct(protected Quiz $quizzes)
    {
    }

    public function findAllPaginatedWithStatus($page = 6)
    {
        return $this->quizzes
            ->when(auth()->user() && auth()->user()->isStudent(), function ($query) {
                $query->whereHas('users', function ($subQuery) {
                    $subQuery->where('users.id', auth()->user()->id);
                });
            })
            ->paginate($page);
    }

    public function findByIdOrLink($id)
    {
        if (is_numeric($id)) {
            $quiz = $this->quizzes->find($id);
        } else {
            $quiz = $this->quizzes->where('link', $id)->first();
        }
        return $quiz;
    }

    public function generatePublicLink(\Filament\Forms\Set $set, $column, $state, ?Quiz $record): void
    {
        if (QuizAccess::valueOf($state) == QuizAccess::PRIVATE) {
            $set($column, '');
        }

        if (QuizAccess::valueOf($state) == QuizAccess::PUBLIC) {
            if ($record == null || $record->link == null) {
                $link = Str::random(20);
                while ($this->quizzes->where('link', $link)->exists()) {
                    $link = Str::random(20);
                }
            }
            if ($record != null && $record->link != null) $link = $record->link;
            $set($column, $link);
        }
    }
}
