<?php

namespace App\Filament\Pages\Monitoring\Books\Widgets;

use App\Models\Book;
use App\Models\Group;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookStats extends BaseWidget
{
    public Book $book;

    protected function getStats(): array
    {
        $recordId = $this->book->id;
        $readingStudentsCount = Group::query()
            ->join('group_user', 'groups.id', '=', 'group_user.group_id')
            ->join('users', 'group_user.user_id', '=', 'users.id')
            ->join('book_user_states', function ($join) use ($recordId) {
                $join->on('users.id', '=', 'book_user_states.user_id')
                    ->where('book_user_states.book_id', $recordId)
                    ->where('book_user_states.status', 'started');
            })
            ->where('users.external_type', 'student')
            ->count('group_user.user_id');
        $pausedStudentsCount = Group::query()
            ->join('group_user', 'groups.id', '=', 'group_user.group_id')
            ->join('users', 'group_user.user_id', '=', 'users.id')
            ->join('book_user_states', function ($join) use ($recordId) {
                $join->on('users.id', '=', 'book_user_states.user_id')
                    ->where('book_user_states.book_id', $recordId)
                    ->where('book_user_states.status', 'paused');
            })
            ->where('users.external_type', 'student')
            ->count('group_user.user_id');
        $finishedStudentsCount = Group::query()
            ->join('group_user', 'groups.id', '=', 'group_user.group_id')
            ->join('users', 'group_user.user_id', '=', 'users.id')
            ->join('book_user_states', function ($join) use ($recordId) {
                $join->on('users.id', '=', 'book_user_states.user_id')
                    ->where('book_user_states.book_id', $recordId)
                    ->where('book_user_states.status', 'finished');
            })
            ->where('users.external_type', 'student')
            ->count('group_user.user_id');
        $notStartedStudentCount = Group::query()
            ->join('book_group', 'groups.id', '=', 'book_group.group_id')
            ->join('group_user', 'groups.id', '=', 'group_user.group_id')
            ->leftJoin('book_user_states', function ($join) use ($recordId) {
                $join->on('group_user.user_id', '=', 'book_user_states.user_id')
                    ->where('book_user_states.book_id', $recordId)
                    ->whereIn('book_user_states.status', ['started', 'paused', 'finished']);
            })
            ->whereNull('book_user_states.user_id')
            ->where('book_group.book_id', $recordId)
            ->count('group_user.user_id');
        $groupStudentCount = Group::query()
            ->join('book_group', 'groups.id', '=', 'book_group.group_id')
            ->join('group_user', 'groups.id', '=', 'group_user.group_id')
            ->where('book_group.book_id', $recordId)
            ->count('group_user.user_id');
        return [
            Stat::make('Сейчас читают', $readingStudentsCount),
            Stat::make('Остановили', $pausedStudentsCount),
            Stat::make('Закончили', $finishedStudentsCount),
            Stat::make('Не начали', $notStartedStudentCount),
            Stat::make('Всего учеников', $groupStudentCount),
        ];
    }
}
