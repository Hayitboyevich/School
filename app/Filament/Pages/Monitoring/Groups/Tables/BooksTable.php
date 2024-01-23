<?php

namespace App\Filament\Pages\Monitoring\Groups\Tables;

use App\Filament\Pages\Monitoring\Groups\StudentsMonitoring;
use App\Filament\Resources\BookResource;
use App\Models\Book;
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
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BooksTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Book $book;

    public Group $group;

    public User $user;

    public function __construct()
    {
        $this->book = Book::first();
    }

    public function render()
    {
        return view('filament.pages.monitoring.groups.tables.books-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BookResource::getEloquentQuery()
                    ->select(['books.id', 'books.name', 'book_group.end_date', 'book_group.score',
                        DB::raw('(SELECT COUNT(DISTINCT group_user.user_id) FROM group_user WHERE group_user.group_id = groups.id) AS student'),
                        DB::raw('(SELECT COUNT(*) FROM book_user_states WHERE book_user_states.book_id = books.id AND book_user_states.status = "started") AS started'),
                        DB::raw('(SELECT COUNT(*) FROM book_user_states WHERE book_user_states.book_id = books.id AND book_user_states.status = "finished") AS finished'),
                        DB::raw('(SELECT COUNT(*) FROM book_user_states WHERE book_user_states.book_id = books.id AND book_user_states.status = "paused") AS paused'),
                        DB::raw(
                            '(SELECT COUNT(DISTINCT group_user.user_id) FROM group_user WHERE group_user.group_id = groups.id) -
                        (
                            SELECT COUNT(DISTINCT book_user_states.user_id)
                            FROM book_user_states
                            JOIN group_user ON book_user_states.user_id = group_user.user_id
                            WHERE book_user_states.book_id = books.id
                                AND group_user.group_id = groups.id
                                AND (
                                    book_user_states.status = "started"
                                    OR book_user_states.status = "finished"
                                    OR book_user_states.status = "paused"
                                )
                        ) AS absent'
                        ),
                        DB::raw(
                            'IF(
                            (SELECT COUNT(DISTINCT group_user.user_id)
                            FROM group_user WHERE group_user.group_id = groups.id) = 0,
                            "0 %",
                            CONCAT(
                                FORMAT(
                                    (IFNULL((SELECT COUNT(*) FROM book_user_states WHERE book_user_states.book_id = books.id AND book_user_states.status = "finished"), 0)
                                    / IFNULL((SELECT COUNT(DISTINCT group_user.user_id) FROM group_user WHERE group_user.group_id = groups.id), 1)) * 100,
                                    2
                                ),
                                " "
                            )
                        ) AS percent'
                        )])
                    ->join('book_group', 'books.id', '=', 'book_group.book_id')
                    ->leftJoin('group_user', 'book_group.group_id', '=', 'group_user.group_id')
                    ->leftJoin('book_user_states', function ($query) {
                        $query->on('group_user.user_id', '=', 'book_user_states.user_id')
                            ->where('book_user_states.book_id', '=', $this->book->id);
                    })
                    ->join('groups', 'book_group.group_id', '=', 'groups.id')
                    ->where('book_group.group_id', '=', $this->group->id)
                    ->groupBy(
                        'books.id',
                        'books.name',
                        'book_group.score',
                        'end_date',
                        'groups.id'
                    )
            )
            ->columns([
                Columns\TextColumn::make('name')->label(__('models/group.prop.books'))->sortable()->searchable(),
                Columns\TextColumn::make('student')->label(__('models/group.action.student'))->sortable(),
                Columns\TextColumn::make('started')->label(__('models/group.action.reading'))->sortable(),
                Columns\TextColumn::make('finished')->label(__('models/group.action.finished'))->sortable(),
                Columns\TextColumn::make('paused')->label(__('models/group.action.stopped'))->sortable(),
                Columns\TextColumn::make('absent')->label(__('models/group.action.not_start'))->sortable(),
                Columns\TextColumn::make('percent')->label(__('models/group.action.progress'))->sortable(),
                Columns\TextColumn::make('score')->label(__('models/group.prop.score'))->sortable(),
                Columns\TextColumn::make('end_date')->label(__('models/group.prop.end_date'))->
                formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y'))->sortable()
            ])
            ->recordUrl(function (Model $record) {
                $bookId = $record->id;
                $groupId = $this->group->id;

                return StudentsMonitoring::getUrl([
                    'groupId' => $groupId,
                    'bookId' => $bookId,
                ]);
            })
            ->defaultPaginationPageOption(10);
    }
}
