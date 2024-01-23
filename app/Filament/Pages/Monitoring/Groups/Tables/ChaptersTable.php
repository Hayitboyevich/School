<?php

namespace App\Filament\Pages\Monitoring\Groups\Tables;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\BookChapterUserState;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Livewire\Component;

class ChaptersTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Book $book;

    public BookChapter $book_chapter;

    public Group $group;

    public User $user;

    public function render()
    {
        return view('filament.pages.monitoring.groups.tables.chapters-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BookChapterUserState::query()
                    ->with(['book_chapter', 'user'])
                    ->join('book_chapters', 'book_chapter_user_states.book_chapter_id', '=', 'book_chapters.id')
                    ->join('book_chapter_group', 'book_chapters.id', '=', 'book_chapter_group.book_chapter_id')
                    ->where('book_chapter_group.group_id', $this->group->id)
                    ->select(
                        'book_chapter_user_states.*',
                        'book_chapter_group.start_date as start_date',
                        'book_chapter_group.end_date as end_date',
                        'book_chapter_group.score as score'
                    )
                    ->whereHas('book_chapter.group', function ($query) {
                        $query->where('groups.id', $this->group->id);
                    })
            )
            ->columns([
                Columns\TextColumn::make('number')->state(fn($column) => $column->getRowLoop()->iteration)->label(__('models/group.prop.number'))->sortable(),
                Columns\TextColumn::make('user.name')->label(__('models/group.prop.one_student'))->sortable()->searchable(),
                Columns\TextColumn::make('book_chapter.name')->label(__('models/group.prop.chapter'))->sortable()->searchable(),
                Columns\TextColumn::make('status')->label(__('models/group.prop.status'))->sortable()->searchable()->badge()
                    ->formatStateUsing(fn(string $state): string => __("models/book_chapter.relation.{$state}"))
                    ->color(fn(string $state): string => match ($state) {
                        'paused' => 'danger',
                        'started' => 'gray',
                        'finished' => 'success',
                    }),
                Columns\TextColumn::make('start_date')
                    ->label(__('models/group.prop.start_date'))
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y'))
                    ->sortable()
                    ->searchable(),
                Columns\TextColumn::make('end_date')
                    ->label(__('models/group.prop.end_date'))
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y'))
                    ->sortable()->searchable(),
                Columns\TextColumn::make('development')->label(__('models/group.prop.development'))->sortable()->searchable(),
                Columns\TextColumn::make('score')
                    ->label(__('models/group.prop.score'))->sortable()->searchable(),
            ])
            ->defaultPaginationPageOption(10);
    }
}
