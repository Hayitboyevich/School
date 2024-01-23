<?php

namespace App\Filament\Pages\Monitoring\Groups\Tables;

use App\Filament\Pages\Monitoring\Groups\GroupMonitoring;
use App\Models\Book;
use App\Models\BookUserState;
use App\Models\Enums\BookUserStatus;
use App\Models\Group;
use App\Services\AcademicYearService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class GroupsTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Book $book;
    private int $academicYearId;

    public function boot(AcademicYearService $academicYearService): void
    {
        $this->academicYearId = $academicYearService->getDefault()->external_id;
    }

    public function render()
    {
        return view('filament.pages.monitoring.groups.tables.groups-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Group::query()
                    ->whereJsonContains('academic_year_ids', $this->academicYearId)
                    ->orderBy('group_level')
                    ->orderBy('group_letter')
                    ->select(['groups.id', 'group_level', 'group_letter'])
            )
            ->filters([
                SelectFilter::make('id')
                    ->label(__('models/user.prop.group'))
                    ->options(
                        Group::query()
                            ->whereJsonContains('academic_year_ids', $this->academicYearId)
                            ->orderBy('group_level')
                            ->orderBy('group_letter')
                            ->get(['id', 'group_level', 'group_letter'])
                            ->map(function ($group) {
                                return [
                                    'id' => $group->id,
                                    'name' => $group->group_level . ' ' . $group->group_letter,
                                ];
                            })
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->multiple()
                    ->preload(),
            ], FiltersLayout::AboveContent)
            ->columns([
                Columns\TextColumn::make('number')->state(fn($column) => $column->getRowLoop()->iteration)->label(__('models/group.prop.number')),
                Columns\TextColumn::make('name')
                    ->label(__('models/group.prop.group'))
                    ->sortable(['group_level', 'group_letter']),
                Columns\TextColumn::make('student')
                    ->state(fn($record) => $record->users->count())
                    ->label(__('models/group.action.student'))->sortable(),
                Columns\TextColumn::make('started')
                    ->state(function ($record, BookUserState $bookUserStates) {
                        return $bookUserStates
                            ->whereIn('user_id', $record->users->pluck('id'))
                            ->where('status', BookUserStatus::STARTED)
                            ->count();
                    })
                    ->label(__('models/group.action.reading'))->sortable
                    (['started'], function ($query, $direction) {
                        return $query->orderByRaw('CAST(SUBSTRING_INDEX(started, " ", -1) AS UNSIGNED) ' . $direction);
                    })
                    ->label(__('models/group.action.reading'))->sortable(['started']),
                Columns\TextColumn::make('paused')
                    ->state(function ($record, BookUserState $bookUserStates) {
                        return $bookUserStates
                            ->whereIn('user_id', $record->users->pluck('id'))
                            ->where('status', BookUserStatus::PAUSED)
                            ->count();
                    })
                    ->label(__('models/group.action.stopped'))->sortable(['paused']),
                Columns\TextColumn::make('finished')
                    ->state(function ($record, BookUserState $bookUserStates) {
                        return $bookUserStates
                            ->whereIn('user_id', $record->users->pluck('id'))
                            ->where('status', BookUserStatus::FINISHED)
                            ->count();
                    })
                    ->label(__('models/group.action.finished'))
                    ->sortable(),
                Columns\TextColumn::make('percent')
                    ->state(function ($record, BookUserState $bookUserStates) {
                        $started = $bookUserStates
                            ->whereIn('user_id', $record->users->pluck('id'))
                            ->where('status', BookUserStatus::STARTED)
                            ->count();
                        if ($record->users->count() === 0) return 0;
                        return ($started / $record->users->count() * 100 . ' %');
                        $total = $record->users->count();
                        if ($total == 0) {
                            return 0;
                        }
                        return (($started / $record->users->count()) * 100 . ' %');
                    })
                    ->label(__('models/group.action.progress'))->sortable(),
            ])
            ->defaultSort('group_level', 'group_letter')
            ->recordUrl(fn(Model $record) => GroupMonitoring::getUrl(['recordId' => $record->id]))
            ->defaultPaginationPageOption(10);
    }
}
