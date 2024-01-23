<?php

namespace App\Filament\Pages\Monitoring\Quizzes\Widgets;

use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuizStats extends BaseWidget
{
    protected static ?string $maxHeight = '200px';

    protected function getStats(): array
    {
        $quizzes = Quiz::query()->with('groups')->get();

        $uniqueGroupsAssignedToQuizzes = $quizzes->flatMap(function ($quiz) {
            return $quiz->groups->pluck('id')->toArray();
        })->unique()->count();
        $totalQuizzessCount = Quiz::query()->count();

        $uniqueUsersAssignedToQuizzes = User::whereHas('groups', function ($query) use ($quizzes) {
            $query->whereIn('groups.id', $quizzes->pluck('groups')->flatten()->pluck('id')->unique());
        })->where('users.external_type', 'student')->count();

        $startedQuizStudent = Attempt::query()->where('status', 'started')->orWhere('status','finished')
            ->whereHas('user', function ($query) {
                $query->where('external_type', 'student');
            })->count();
        $finishedQuizStudent = $uniqueUsersAssignedToQuizzes - $startedQuizStudent;

        return [
            Stat::make('Всего классов', $uniqueGroupsAssignedToQuizzes),
            Stat::make('Всего тестов', $totalQuizzessCount),
            Stat::make('Всего учеников', $uniqueUsersAssignedToQuizzes),
            Stat::make('Сдали', $startedQuizStudent),
            Stat::make('Не сдали', $finishedQuizStudent),
        ];
    }
}
