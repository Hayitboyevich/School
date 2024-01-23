<?php

namespace App\Filament\Pages\Monitoring\Groups\Widgets;

use App\Models\Book;
use App\Models\BookUserState;
use App\Models\Group;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookStates extends BaseWidget
{
    protected static ?string $maxHeight = '100px';

    public Group $group;

    public Book $book;

    protected function getStats(): array
    {
        $groupId = $this->group->id;
        $bookId = $this->book->id;

        $readingStudentsCount = BookUserState::query()
            ->join('group_user', 'book_user_states.user_id', '=', 'group_user.user_id')
            ->where('group_user.group_id', $groupId)
            ->where('book_user_states.status', 'started')
            ->where('book_user_states.book_id', $bookId)
            ->count();

        $pausedStudentsCount = BookUserState::query()
            ->join('group_user', 'book_user_states.user_id', '=', 'group_user.user_id')
            ->where('group_user.group_id', $groupId)
            ->where('book_user_states.status', 'paused')
            ->where('book_user_states.book_id', $bookId)
            ->count();
        $finishedStudentsCount = BookUserState::query()
            ->join('group_user', 'book_user_states.user_id', '=', 'group_user.user_id')
            ->where('group_user.group_id', $groupId)
            ->where('book_user_states.status', 'finished')
            ->where('book_user_states.book_id', $bookId)
            ->count();
        $groupStudentCount = User::query()
            ->where('external_type', 'student')
            ->whereHas('groups', function ($query) use ($groupId) {
                $query->where('groups.id', $groupId);
            })->count();

        $noCount = $groupStudentCount - ($readingStudentsCount + $pausedStudentsCount + $finishedStudentsCount);

        return
            [
                Stat::make('Сейчас читают', $readingStudentsCount),
                Stat::make('Остановили', $pausedStudentsCount),
                Stat::make('Закончили', $finishedStudentsCount),
                Stat::make('Не начала', $noCount),
                Stat::make('Всего учеников', $groupStudentCount)
            ];
    }
}

