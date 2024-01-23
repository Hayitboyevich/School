<?php

namespace App\Filament\Pages\Monitoring\Quizzes\Tables;

use App\Filament\Pages\Monitoring\Quizzes\StudentMonitoring;
use App\Models\Group;
use App\Models\Quiz;
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

class StudentsTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Group $group;

    public Quiz $quiz;

    public function __construct()
    {
        $this->quiz = Quiz::first();
//            $this->quiz = Group::first();
    }

    public function render()
    {
        return view('filament.pages.monitoring.quizzes.tables.students-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()
                ->select(['users.id', 'users.name'])
                ->where('external_type', 'student')
                ->whereHas('groups', function ($query) {
                    $query->where('groups.id', $this->group->id);
                })->with(['attempts' => function ($query) {
                    $query->where('quiz_id', $this->quiz->id)
                        ->select('user_id', 'status', 'start_date', 'end_date');
                }])
            )
            ->columns([
                Columns\TextColumn::make('name')->label(__('monitoring.quizzes.students.name'))->sortable(['name'])->searchable(),
                Columns\TextColumn::make('assessment')
                    ->state(function ($record) {
                        return __('Отлично');
                    })->label(__('monitoring.quizzes.students.assessment'))->sortable(),
//                Columns\TextColumn::make('status')
//                    ->state(function (User $user) {
//                return $user->attempts->first()->status ?? 'Not Attempted';
//            })->label(__('monitoring.quizzes.students.status'))->sortable(),
                Columns\TextColumn::make('answer')
                    ->state(function ($record) {
                        return __('17/20');
                    })->label(__('monitoring.quizzes.students.answer'))->sortable(),
                Columns\TextColumn::make('ball')
                    ->state(function ($record) {
                        return __('30');
                    })->label(__('monitoring.quizzes.quiz.ball'))->sortable(),
                Columns\TextColumn::make('development')
                    ->state(function ($record) {
                        return __('Хорошо');
                    })->label(__('monitoring.quizzes.quiz.development'))->sortable(),
                Columns\TextColumn::make('time')->label(__('monitoring.quizzes.quiz.time'))->state(function ($record) {
                    return __('25 мин');
                })->sortable(),
                Columns\TextColumn::make('start_date')
                    ->state(function (User $user) {
                        return $user->attempts->first()->start_date ?? '';
                    })->label(__('monitoring.quizzes.students.due_date'))
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('DD.MM.Y HH:mm'))->sortable(),
                Columns\TextColumn::make('transfer_date')->label(__('monitoring.quizzes.students.transfer_date'))->sortable(),
            ])
            ->recordUrl(function (Model $record) {
                $userId = $record->id;
                $quizId = $this->quiz->id;
                $groupId = $this->group->id;
                return StudentMonitoring::getUrl([
                    'groupId' => $groupId,
                    'quizId' => $quizId,
                    'userId' => $userId
                ]);
            })
            ->defaultPaginationPageOption(10);
    }
}
