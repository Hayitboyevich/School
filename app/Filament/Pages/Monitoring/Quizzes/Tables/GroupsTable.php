<?php

namespace App\Filament\Pages\Monitoring\Quizzes\Tables;

use App\Filament\Pages\Monitoring\Quizzes\StudentsMonitoring;
use App\Models\Group;
use App\Models\Quiz;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

class GroupsTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Group $group;
    public Quiz $quiz;

    public $sortBy = ''; // Initialize the sortBy property

    public $sortDirection = 'asc'; // Default sort direction

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }

    public function __construct()
    {
        $this->quiz = Quiz::first();
    }

    public function render()
    {
        return view('filament.pages.monitoring.quizzes.tables.groups-table');
    }

    public function table(Table $table): Table
    {
        $currentQuiz = $this->quiz;

        return $table
            ->query(Group::query()
                ->whereHas('quizzes', function ($query) use ($currentQuiz) {
                    $query->where('quiz_id', $currentQuiz->id);
                })
                ->whereNotNull('academic_year_ids')
            )->defaultSort('group_level')
            ->columns([
                Columns\TextColumn::make('name')->label(__('monitoring.quizzes.group.class'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where(function (Builder $query) use ($search) {
                                $query->where('group_level', 'like', "%{$search}%")
                                    ->orWhere('group_letter', 'like', "%{$search}%");
                            });
                    }),
                Columns\TextColumn::make('student')->state(fn($record) => $record->users->where('external_type', 'student')->count())
                    ->label(__('monitoring.quizzes.group.student')),
                Columns\TextColumn::make('passed')
                    ->state(function (Group $group) use ($currentQuiz) {
                        return $group->users()
                            ->whereHas('attempts', function ($query) use ($currentQuiz) {
                                $query->where('status', 'finished')
                                    ->where('quiz_id', $currentQuiz->id);
                            })
                            ->count();
                    })->label(__('monitoring.quizzes.group.passed')),
                Columns\TextColumn::make('no_passed')
                    ->state(function (Group $group) use ($currentQuiz) {
                        $totalStudents = $group->users->where('external_type', 'student')->count();
                        $passedStudents = $group->users()
                            ->whereHas('attempts', function ($query) use ($currentQuiz) {
                                $query->where('status', 'finished')
                                    ->where('quiz_id', $currentQuiz->id);
                            })
                            ->count();
                        return $totalStudents - $passedStudents;
                    })->label(__('monitoring.quizzes.group.no_passed')),
//                Columns\TextColumn::make('no_started')->label(__('monitoring.quizzes.group.no_started'))->sortable(),
                Columns\TextColumn::make('time')
                    ->state(function (Group $group) {
                        $quiz = $group->quizzes()->first();
                        if ($quiz) {
                            return $quiz->quiz_time;
                        }
                        return null;
                    })
                    ->label(__('monitoring.quizzes.group.time')),
                Columns\TextColumn::make('answer')->label(__('monitoring.quizzes.group.answer'))->sortable(),
                Columns\TextColumn::make('rating')->label(__('monitoring.quizzes.group.rating')),
                Columns\TextColumn::make('end_date')
                    ->state(function (Group $group) {
                        $quiz = $group->quizzes()->first();
                        if ($quiz) {
                            return $quiz->end_date;
                        }
                        return null;
                    })
                    ->label(__('monitoring.quizzes.group.end_date')),
            ])
            ->recordUrl(function (Model $record) {
                $groupId = $record->id;
                $recordId = $this->quiz->id;
                return StudentsMonitoring::getUrl([
                    'groupId' => $groupId,
                    'recordId' => $recordId,
                ]);
            })
            ->defaultPaginationPageOption(10);
    }
}
