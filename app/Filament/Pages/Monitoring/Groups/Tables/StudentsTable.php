<?php

namespace App\Filament\Pages\Monitoring\Groups\Tables;

use App\Filament\Pages\StudentBookMonitoring;
use App\Models\Book;
use App\Models\BookUserState;
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

class StudentsTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Book $book;

    public Group $group;

    public User $user;

    public function render()
    {
        return view('filament.pages.monitoring.groups.tables.students-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                  BookUserState::query()
                ->with(['book', 'user'])
                ->join('books', 'book_user_states.book_id', '=', 'books.id')
                ->join('book_group', 'books.id', '=', 'book_group.book_id')
                ->join('users', 'book_user_states.user_id', '=', 'users.id')
                ->where('book_group.group_id', $this->group->id)
                ->where('users.external_type', 'student')
                ->select('book_user_states.*', 'book_group.start_date', 'book_group.end_date', 'book_group.score')
                ->whereHas('book.groups', function ($query) {
                    $query->where('groups.id', $this->group->id);
                })
            )
            ->columns([
                Columns\TextColumn::make('number')->state(fn($column) => $column->getRowLoop()->iteration)->label(__('models/group.prop.number'))->sortable(),
                Columns\TextColumn::make('user.name')
                    ->label(__('models/group.prop.one_student'))->sortable()->searchable(),
                Columns\TextColumn::make('book.name')->label(__('models/group.prop.book'))->sortable()->searchable()->wrap(),
                Columns\TextColumn::make('status')->label(__('models/group.prop.status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => __("models/book_chapter.relation.{$state}"))
                    ->color(fn(string $state): string => match ($state) {
                        'paused' => 'danger',
                        'started' => 'gray',
                        'finished' => 'success',
                    })
                    ->sortable()->searchable(),
                Columns\TextColumn::make('start_date')
                    ->label(__('models/group.prop.start_date'))
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y'))
                    ->sortable()->searchable(),
                Columns\TextColumn::make('end_date')
                    ->label(__('models/group.prop.end_date'))
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y'))
                    ->sortable()->searchable(),
                Columns\TextColumn::make('development')->label(__('models/group.prop.development'))->sortable()->searchable(),
                Columns\TextColumn::make('score')
                    ->label(__('models/group.prop.score'))
                    ->sortable()
                    ->searchable(),
            ])
            ->recordUrl(function (Model $record) {
                $userId = $record->user->id;
                return StudentBookMonitoring::getUrl(['recordId' => $record->id, 'userId' => $userId]);
            })
            ->defaultPaginationPageOption(10);
    }
}
