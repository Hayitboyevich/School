<?php

namespace App\Filament\Pages\Monitoring\Readers\Tables;

use App\Models\Enums\BookUserStatus;
use App\Models\User;
use App\Services\AcademicYearService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FinishedReadersTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    private AcademicYearService $academicYearService;

    public function boot(AcademicYearService $academicYearService): void
    {
        $this->academicYearService = $academicYearService;
    }

    public function render()
    {
        return view('filament.pages.monitoring.readers.tables.finished-readers-table');
    }

    public function table(Table $table): Table
    {
        $academic_year_id = $this->academicYearService->getDefault()->external_id;

        return $table
            ->query(User::query()
                ->select([
                    'users.id',
                    DB::raw(
                        'GREATEST(
                            MAX(book_user_states.date),
                            MAX(book_chapter_user_states.date)
                        ) AS date'
                    )
                ])
                ->addSelect(DB::raw(
                    'COALESCE(
                        NULLIF(
                            CONCAT_WS(" ", users.first_name, users.last_name, users.middle_name), ""
                            ),
                        users.name
                    ) AS full_name'
                ))
                ->addSelect(DB::raw(
                    'COALESCE(
                        CONCAT_WS(" ", groups.group_level, groups.group_letter), groups.group_id
                    ) AS `group`'
                ))
                ->addSelect(DB::raw('GROUP_CONCAT(DISTINCT TRIM(books.name) SEPARATOR ", ") AS book_names'))
                ->addSelect(DB::raw('GROUP_CONCAT(DISTINCT TRIM(book_chapters.name) SEPARATOR ", ") AS book_chapter_names'))
                ->join('book_user_states', 'book_user_states.user_id', 'users.id')
                ->leftJoin('group_user', 'group_user.user_id', 'users.id')
                ->leftJoin('groups', function ($query) use ($academic_year_id) {
                    $query->on('group_user.group_id', 'groups.id')
                        ->whereJsonContains('groups.academic_year_ids', $academic_year_id);
                })
                ->leftJoin('books', 'books.id', 'book_user_states.book_id')
                ->leftJoin('book_chapter_user_states', 'book_chapter_user_states.user_id', 'users.id')
                ->leftJoin('book_chapters', 'book_chapters.id', 'book_chapter_user_states.book_chapter_id')
                ->where('book_user_states.status', BookUserStatus::FINISHED)
                ->where(function ($query) {
                    $query->where('book_chapter_user_states.status', BookUserStatus::FINISHED)
                        ->orWhereNull('book_chapter_user_states.id');
                })
                ->groupBy(
                    'users.id',
                    'groups.group_id',
                    'groups.group_level',
                    'groups.group_letter'
                )
            )
            ->columns([
                TextColumn::make('full_name')->label(__('monitoring.readers.prop.full_name')),
                TextColumn::make('group')->label(__('monitoring.readers.prop.group')),
                TextColumn::make('book_names')->label(__('monitoring.readers.prop.book_names'))->wrap(),
                TextColumn::make('book_chapter_names')->label(__('monitoring.readers.prop.book_chapter_names'))->wrap(),
                TextColumn::make('date')->label(__('monitoring.readers.prop.date'))
            ]);
    }
}
