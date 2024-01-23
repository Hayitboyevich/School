<?php

namespace App\Filament\Pages\Monitoring\Groups\Tables;

use App\Models\BookChapterUserState;
use App\Models\User;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class StudentBookOverview extends BaseWidget
{
    protected function paginateTableQuery(Builder $query): LengthAwarePaginator
    {
        return $query->paginate((int)$this->getTableRecordsPerPage());
    }

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '';

    public User $user;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BookChapterUserState::query()->where('user_id',   $this->user->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('book_chapter.book.name')->label(__('models/book.singular')),
                Tables\Columns\TextColumn::make('book_chapter.name')->label(__('models/book.relation.book_chapter')),
                Tables\Columns\TextColumn::make('status')->label(__('models/book.prop.status'))->badge()
                    ->formatStateUsing(fn (string $state): string => __("models/book_chapter.relation.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'paused' => 'danger',
                        'started' => 'gray',
                        'finished' => 'success',
                    }),
                Tables\Columns\TextColumn::make('book_chapter.start_date')->label(__('models/group.prop.start_date'))
                    ->formatStateUsing(fn (string $state): string => Carbon::parse($state)->isoFormat('DD.MM.Y')),
                Tables\Columns\TextColumn::make('book_chapter.end_date')->label(__('models/group.prop.end_date'))
                    ->formatStateUsing(fn (string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y')),
                Tables\Columns\TextColumn::make('book_chapter.score')->label(__('models/book.prop.score')),
            ]);
    }
}
