<?php

namespace App\Filament\Pages\Monitoring\Books\Widgets;

use App\Models\Book;
use App\Models\BookUserState;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BooksStats extends BaseWidget
{
    protected static ?string $maxHeight = '100px';

    protected function getStats(): array
    {
        $startedBooksCount = BookUserState::query()
            ->join('users', 'book_user_states.user_id', '=', 'users.id')
            ->where('book_user_states.status', 'started')
            ->where('users.external_type', 'student')
            ->selectRaw('COUNT(DISTINCT book_user_states.book_id) as count')
            ->first()
            ->count;
        $pausedBooksCount = BookUserState::query()
            ->join('users', 'book_user_states.user_id', '=', 'users.id')
            ->where('book_user_states.status', 'paused')
            ->where('users.external_type', 'student')
            ->selectRaw('COUNT(DISTINCT book_user_states.book_id) as count')
            ->first()
            ->count;
        $finishedBooksCount = BookUserState::query()
            ->join('users', 'book_user_states.user_id', '=', 'users.id')
            ->where('book_user_states.status', 'finished')
            ->where('users.external_type', 'student')
            ->selectRaw('COUNT(DISTINCT book_user_states.book_id) as count')
            ->first()
            ->count;
        $notStartedBooksCount = Book::query()
            ->whereNotIn('id', function ($query) {
                $query->select('book_id')->from('book_user_states')
                    ->join('users', 'book_user_states.user_id', '=', 'users.id')
                    ->where('users.external_type', 'student');
            })
            ->count();
        $totalBooksCount = Book::query()->count();

        return [
            Stat::make('Сейчас читают', $startedBooksCount),
            Stat::make('Остановили чтение', $pausedBooksCount),
            Stat::make('Прочитано книг', $finishedBooksCount),
            Stat::make('Не начали', $notStartedBooksCount),
            Stat::make('Всего книг', $totalBooksCount),
        ];
    }
}
