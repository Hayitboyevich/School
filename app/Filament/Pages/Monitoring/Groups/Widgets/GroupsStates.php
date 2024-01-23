<?php

namespace App\Filament\Pages\Monitoring\Groups\Widgets;

use App\Models\Book;
use App\Models\BookUserState;
use App\Models\Group;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class GroupsStates extends BaseWidget
{
    protected static ?string $maxHeight = '100px';

    public Group $group;

    protected function getStats(): array
    {
        $recordId = $this->group->id;

        $readingStudentsCount = DB::table('book_user_states')
            ->join('group_user', 'book_user_states.user_id', '=', 'group_user.user_id')
            ->where('group_user.group_id', $recordId)
            ->where('book_user_states.status', 'started')
            ->count();
        $pausedStudentsCount = DB::table('book_user_states')
            ->join('group_user', 'book_user_states.user_id', '=', 'group_user.user_id')
            ->where('group_user.group_id', $recordId)
            ->where('book_user_states.status', 'paused')
            ->count();
        $finishedStudentsCount = DB::table('book_user_states')
            ->join('group_user', 'book_user_states.user_id', '=', 'group_user.user_id')
            ->where('group_user.group_id', $recordId)
            ->where('book_user_states.status', 'finished')
            ->count();
        $groupStudentCount = User::whereHas('groups', function ($query) use ($recordId) {
            $query->where('groups.id', $recordId);
        })->count();

        return
            [
                Stat::make('Сейчас читают', $readingStudentsCount)
                    ->chart([7, 2, 10, 3, 15, 4, 17])
                    ->color('success'),
                Stat::make('Остановили', $pausedStudentsCount)
                    ->chart([7, 2, 10, 3, 15, 4, 1])
                    ->color('gray'),
                Stat::make('Закончили', $finishedStudentsCount)
                    ->chart([7, 2, 8, 3, 2, 4, 1])
                    ->color('danger'),
                Stat::make('Всего учеников', $groupStudentCount)
                    ->chart([7, 2, 8, 3, 2, 4, 1])
                    ->color('yellow'),
            ];
    }
}
