<?php

namespace App\Filament\Pages\Monitoring\Books\Tables;

use App\Filament\Resources\GroupResource;
use App\Models\Book;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GroupsTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Book $book;

    public function getTableRecordKey(Model $record): string
    {
        return 'group_id';
    }

    public function render()
    {
        return view('filament.pages.monitoring.books.tables.groups-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(GroupResource::getEloquentQuery()
                ->select(['groups.id', 'group_level', 'group_letter'])
                ->addSelect(DB::raw('(SELECT COUNT(DISTINCT group_user.user_id)) AS student'))
                ->addSelect(DB::raw('SUM(CASE WHEN book_user_states.status = \'started\' THEN 1 ELSE 0 END) AS started'))
                ->addSelect(DB::raw('SUM(CASE WHEN book_user_states.status = \'paused\' THEN 1 ELSE 0 END) AS paused'))
                ->addSelect(DB::raw('SUM(CASE WHEN book_user_states.status = \'finished\' THEN 1 ELSE 0 END) AS finished'))
                ->addSelect(DB::raw(
                    'COUNT(DISTINCT group_user.user_id) -
                   SUM(
                       CASE WHEN book_user_states.status = \'started\'
                        OR book_user_states.status = \'finished\'
                        OR book_user_states.status = \'paused\'
                           THEN 1 ELSE 0 END
                   )
                  AS absent'
                ))
                ->addSelect(DB::raw(
                    'IF((SELECT COUNT(DISTINCT group_user.user_id)
                    FROM group_user WHERE group_user.group_id = groups.id) = 0, "0%",
                     CONCAT(FORMAT((SUM(CASE WHEN book_user_states.status = \'finished\' THEN 1 ELSE 0 END)
                      / (SELECT COUNT(DISTINCT group_user.user_id)
                         FROM group_user WHERE group_user.group_id = groups.id)) * 100, 2), "%")
                     )
                   AS percent'
                ))
                ->selectSub(function ($query) {
                    $query->select('books.end_date')
                        ->from('book_group')
                        ->join('books', 'book_group.book_id', '=', 'books.id')
                        ->whereColumn('book_group.group_id', 'groups.id')
                        ->where('books.id', $this->book->id)
                        ->limit(1);
                }, 'end_date')
                ->join('book_group', 'groups.id', 'book_group.group_id')
                ->leftJoin('group_user', 'groups.id', 'group_user.group_id')
                ->leftJoin('book_user_states', function ($query) {
                    $query->on('group_user.user_id', 'book_user_states.user_id')
                        ->where('book_user_states.book_id', $this->book->id);
                })
                ->join('books', 'book_group.book_id', 'books.id')
                ->where('book_group.book_id', $this->book->id)
                ->groupBy('groups.id')
            )
            ->defaultSort('group_level')
            ->columns([
                Columns\TextColumn::make('name')->label(__('models/group.prop.group'))->sortable()->searchable(),
                Columns\TextColumn::make('student')->label(__('models/group.action.student'))->sortable(),
                Columns\TextColumn::make('started')->label(__('models/group.action.reading'))->sortable(),
                Columns\TextColumn::make('paused')->label(__('models/group.action.stopped'))->sortable(),
                Columns\TextColumn::make('finished')->label(__('models/group.action.finished'))->sortable(),
                Columns\TextColumn::make('absent')->label(__('models/group.action.not_start'))->sortable(),
                Columns\TextColumn::make('percent')->label(__('models/group.action.progress'))->sortable(),
                Columns\TextColumn::make('end_date')->label(__('monitoring.books.action.end_date'))->formatStateUsing(fn($state) => human_date($state))->sortable(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
