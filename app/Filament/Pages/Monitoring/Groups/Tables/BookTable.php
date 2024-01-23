<?php

namespace App\Filament\Pages\Monitoring\Groups\Tables;

use App\Models\Book;
use App\Models\BookUserLog;
use App\Models\BookUserState;
use App\Models\Enums\BookUserStatus;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class BookTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Book $book;

    public Group $group;

    public User $user;

    public function render()
    {
        return view('filament.pages.monitoring.groups.tables.book-table');
    }

    public function table(Table $table): Table
    {
        $groupId = $this->group->id;
        $bookId = $this->book->id;

        return $table
            ->query(
                User::query()
                    ->where('external_type', 'student')
                    ->join('group_user', 'users.id', '=', 'group_user.user_id')
                    ->join('book_group', function ($join) use ($bookId, $groupId) {
                        $join->on('book_group.group_id', '=', 'group_user.group_id')
                            ->where('book_group.book_id', '=', $bookId)
                            ->where('group_user.group_id', '=', $groupId);
                    })
                    ->select('users.*', 'book_group.start_date', 'book_group.end_date')
            )
            ->columns([
                Columns\TextColumn::make('number')->state(fn($column) => $column->getRowLoop()->iteration)->label(__('models/group.prop.number')),
                Columns\TextColumn::make('name')
                    ->state(function (User $user) {
                        return $user->name;
                    })
                    ->label(__('models/group.prop.one_student'))->sortable()->searchable(),
                Columns\TextColumn::make('status')
                    ->label(__('models/group.prop.status'))
                    ->state(function (User $user) use ($bookId) {
                        $status = $user->books->firstWhere('id', $bookId)->pivot->status ?? 'Не начал(а)';
                        return $status;
                    })
                    ->badge()
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'paused' => __("models/book_chapter.relation.paused"),
                            'started' => __("models/book_chapter.relation.started"),
                            'finished' => __("models/book_chapter.relation.finished"),
                            default => __("models/book_chapter.relation.no_started"),
                        };
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'paused' => 'danger',
                        'started' => 'gray',
                        'finished' => 'success',
                        default => 'info',
                    })
                    ->sortable('status')
                    ->searchable(),
                Columns\TextColumn::make('start_date')
                    ->label(__('models/group.prop.start_date'))
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y')),
                Columns\TextColumn::make('end_date')
                    ->label(__('models/group.prop.end_date'))
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y')),
                Columns\TextColumn::make('date_start')
                    ->label(__('models/group.prop.start'))
                    ->state(function (User $user) use ($bookId) {
                        $dateStart = $user->states()
                            ->where('status', BookUserStatus::STARTED)
                            ->first()?->pivot->date ?? null;
                        return $dateStart ? Carbon::parse($dateStart)->locale('ru')->isoFormat('DD.MM.Y HH:mm') : '';
                    })->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y HH:mm'))
                  ->sortable('book_user_logs.date', function ($query, $direction) {
                        $query->join('book_user_logs', function ($join) use ($direction) {
                            $join->on('users.id', '=', 'book_user_logs.user_id')
                                ->where('book_user_logs.status', BookUserStatus::STARTED)
                                ->orderBy('book_user_logs.date', $direction);
                        });
                    }),
                Columns\TextColumn::make('date_finish')
                    ->label(__('models/group.prop.finish'))
                    ->state(function (User $user) use ($bookId) {
                        $dateStart = $user->states()
                            ->where('status', BookUserStatus::FINISHED)
                            ->first()?->pivot->date ?? null;
                        return $dateStart ? Carbon::parse($dateStart)->locale('ru')->isoFormat('DD.MM.Y HH:mm') : '';
                    })->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y HH:mm'))
                 ->sortable('book_user_logs.date', function ($query, $direction) {
                        $query->join('book_user_logs', function ($join) use ($direction) {
                            $join->on('users.id', '=', 'book_user_logs.user_id')
                                ->where('book_user_logs.status', BookUserStatus::FINISHED)
                                ->orderBy('book_user_logs.date', $direction);
                        });
                    }),
            ])
            ->defaultPaginationPageOption(25);
    }
}


