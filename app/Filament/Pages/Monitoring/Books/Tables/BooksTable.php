<?php

namespace App\Filament\Pages\Monitoring\Books\Tables;

use App\Filament\Pages\Monitoring\Books\BookMonitoring;
use App\Filament\Resources\BookResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BooksTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function render()
    {
        return view('filament.pages.monitoring.books.tables.books-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(BookResource::getEloquentQuery()
                ->select(['books.id', 'name', 'cover', 'books.end_date'])
                ->addSelect(DB::raw('(SELECT COUNT(DISTINCT group_user.user_id) FROM group_user
                    JOIN book_group ON group_user.group_id = book_group.group_id
                    WHERE book_group.book_id = books.id) AS total'))
                ->addSelect(DB::raw('SUM(CASE WHEN started_states.status = \'started\' THEN 1 ELSE 0 END) AS started'))
                ->addSelect(DB::raw('SUM(CASE WHEN paused_states.status = \'paused\' THEN 1 ELSE 0 END) AS paused'))
                ->addSelect(DB::raw('SUM(CASE WHEN finished_states.status = \'finished\' THEN 1 ELSE 0 END) AS finished'))
                ->addSelect(DB::raw('(SELECT COUNT(DISTINCT group_user.user_id) FROM group_user
                         JOIN book_group ON group_user.group_id = book_group.group_id
                         WHERE book_group.book_id = books.id)
                         - SUM(
                             CASE WHEN started_states.status = \'started\'
                             OR paused_states.status = \'paused\'
                             OR finished_states.status = \'finished\'
                             THEN 1 ELSE 0 END
                         ) AS difference'))
                ->addSelect(DB::raw('
                       CONCAT(
                         IF(
                            (SELECT COUNT(DISTINCT group_user.user_id)
                             FROM group_user
                             JOIN book_group ON group_user.group_id = book_group.group_id
                             WHERE book_group.book_id = books.id) = 0, "0.00",
                        CONCAT(FORMAT(
                        (SUM(CASE WHEN finished_states.status = \'finished\' THEN 1 ELSE 0 END) /
                          (SELECT COUNT(DISTINCT group_user.user_id)
                          FROM group_user
                          JOIN book_group ON group_user.group_id = book_group.group_id
                          WHERE book_group.book_id = books.id)
                          ) * 100, 2))
                          ))
                       AS percent'))
                ->leftJoin('book_group', 'books.id', 'book_group.book_id')
                ->leftJoin('group_user', 'book_group.group_id', 'group_user.group_id')
                ->leftJoin('book_user_states as started_states', function ($join) {
                    $join->on('group_user.user_id', 'started_states.user_id')
                        ->where('started_states.book_id', '=', DB::raw('books.id'))
                        ->where('started_states.status', '=', 'started');
                })
                ->leftJoin('book_user_states as paused_states', function ($join) {
                    $join->on('group_user.user_id', 'paused_states.user_id')
                        ->where('paused_states.book_id', '=', DB::raw('books.id'))
                        ->where('paused_states.status', '=', 'paused');
                })
                ->leftJoin('book_user_states as finished_states', function ($join) {
                    $join->on('group_user.user_id', 'finished_states.user_id')
                        ->where('finished_states.book_id', '=', DB::raw('books.id'))
                        ->where('finished_states.status', '=', 'finished');
                })
                ->groupBy('books.id')
            )
            ->striped()
            ->columns([
                Columns\ImageColumn::make('cover')->label(__('models/book.prop.cover'))->width(40)->height(60)->sortable(),
                Columns\TextColumn::make('name')->label(__('monitoring.books.action.name'))->sortable()->searchable(),
                Columns\TextColumn::make('total')->label(__('monitoring.books.action.student'))->sortable(),
                Columns\TextColumn::make('started')->label(__('monitoring.books.action.reading'))->sortable(),
                Columns\TextColumn::make('paused')->label(__('monitoring.books.action.stopped'))->sortable(),
                Columns\TextColumn::make('finished')->label(__('monitoring.books.action.finished'))->sortable(),
                Columns\TextColumn::make('difference')->label(__('monitoring.books.action.not_start'))->sortable(),
                Columns\TextColumn::make('percent')->label(__('monitoring.books.action.progress'))->sortable(),
                Columns\TextColumn::make('end_date')->label(__('monitoring.books.action.end_date'))->formatStateUsing(fn($state) => human_date($state))->sortable(),
            ])
            ->recordUrl(fn(Model $record) => BookMonitoring::getUrl(['recordId' => $record->id]))
            ->defaultPaginationPageOption(25);
    }
}
