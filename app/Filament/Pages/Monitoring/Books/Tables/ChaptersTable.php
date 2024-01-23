<?php

namespace App\Filament\Pages\Monitoring\Books\Tables;

use App\Models\Book;
use App\Models\BookChapter;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ChaptersTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Book $book;

    public function render()
    {
        return view('filament.pages.monitoring.books.tables.chapters-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(BookChapter::query()
                ->where('book_id', $this->book->id)
                ->select(['book_chapters.id', 'order', 'name', 'end_date'])
                ->addSelect(DB::raw('(SELECT COUNT(DISTINCT group_user.user_id) FROM group_user
                JOIN book_group ON group_user.group_id = book_group.group_id
                WHERE book_group.book_id = book_chapters.book_id) AS total'))
                ->addSelect(DB::raw('SUM(CASE WHEN book_chapter_user_states.status = \'started\' THEN 1 ELSE 0 END) AS started'))
                ->addSelect(DB::raw('SUM(CASE WHEN book_chapter_user_states.status = \'paused\' THEN 1 ELSE 0 END) AS paused'))
                ->addSelect(DB::raw('SUM(CASE WHEN book_chapter_user_states.status = \'finished\' THEN 1 ELSE 0 END) AS finished'))
                ->addSelect(DB::raw('
                            CONCAT(
                               ROUND((SUM(CASE WHEN book_chapter_user_states.status = "finished" THEN 1 ELSE 0 END) /
                               (SELECT COUNT(DISTINCT group_user.user_id) FROM group_user
                               JOIN book_group ON group_user.group_id = book_group.group_id
                               WHERE book_group.book_id = book_chapters.book_id)) * 100, 2), "%"
                            ) AS percent'
                ))
                ->leftJoin('book_chapter_user_states', 'book_chapters.id', 'book_chapter_user_states.book_chapter_id')
                ->groupBy('book_chapters.id')
            )
            ->striped()
            ->columns([
                Columns\TextColumn::make('name')->searchable()->label(__('monitoring.books.chapters.action.name'))->sortable(),
                Columns\TextColumn::make('total')->label(__('monitoring.books.chapters.action.student'))->sortable(),
                Columns\TextColumn::make('started')->label(__('monitoring.books.chapters.action.reading'))->sortable(),
                Columns\TextColumn::make('paused')->label(__('monitoring.books.chapters.action.stopped'))->sortable(),
                Columns\TextColumn::make('finished')->label(__('monitoring.books.chapters.action.finished'))->sortable(),
                Columns\TextColumn::make('percent')->label(__('monitoring.books.chapters.action.progress'))->sortable(),
                Columns\TextColumn::make('end_date')->label(__('monitoring.books.chapters.action.end_date'))->formatStateUsing(fn($state) => human_date($state))->sortable(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
