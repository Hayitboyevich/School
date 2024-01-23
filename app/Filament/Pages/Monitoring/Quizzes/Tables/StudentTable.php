<?php

namespace App\Filament\Pages\Monitoring\Quizzes\Tables;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\AttemptQuestion;
use App\Models\Group;
use App\Models\Quiz;
use App\Models\User;
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

class StudentTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Group $group;
    public Quiz $quiz;
    public User $user;


    public function __construct()
    {
        $this->quiz = Quiz::first();
    }

    public function render()
    {
        return view('filament.pages.monitoring.quizzes.tables.student-table');
    }

    public function table(Table $table): Table
    {
        $attemptIds = Attempt::where('user_id', $this->user->id)->pluck('id');

        return $table
            ->query(AttemptQuestion::query()
                ->whereIn('attempt_id', $attemptIds)
                ->whereHas('attempt_answers', function ($query) {
                    $query->where('is_selected', 1);
                })
                ->whereIn('attempt_id', $attemptIds)
                ->whereHas('attempt_answers', function ($query) {
                    $query->where('is_correct', 1);
                })
                ->whereHas('attempt', function ($query) {
                    $query->where('quiz_id', $this->quiz->id);
                })
            )
            ->columns([
                Columns\TextColumn::make('content')->label(__('monitoring.quizzes.quiz.question'))->html()->searchable(),
                Columns\TextColumn::make('attempt_answers.is_correct')
                    ->state(function (AttemptQuestion $record) {
                        $isCorrect = $record->attempt_answers()->where('is_correct', 1)->first();
                        return $isCorrect ? $isCorrect->content : '';
                    })
                    ->label(__('monitoring.quizzes.quiz.correct_answer'))->html()->searchable(),
                Columns\TextColumn::make('attempt_answers.is_selected')
                    ->state(function (AttemptQuestion $record) {
                        $isCorrect = $record->attempt_answers()->where('is_selected', 1)->first();
                        return $isCorrect ? $isCorrect->content : '';
                    })->label(__('monitoring.quizzes.quiz.your_answer'))->html()->searchable(),
                Columns\TextColumn::make('status')
                        ->state(function (AttemptQuestion $record) {
                    $isCorrect = $record->attempt_answers()->where('is_correct', 1)->first();
                    $isSelected = $record->attempt_answers()->where('is_selected', 1)->first();
                    if ($isSelected==$isCorrect){
                        return 'Верный';
                    } return 'Неверный' ;
                })->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Неверный' => 'primary',
                        'Верный' => 'success',
                    })
                    ->label(__('monitoring.quizzes.quiz.status'))->html()->searchable(),
                Columns\TextColumn::make('time')->state(function ($record) {
                    return __('2 мин');
                })->label(__('monitoring.quizzes.quiz.time'))->html()->searchable(),
                Columns\TextColumn::make('attempt_answers.score')
                    ->state(function (AttemptQuestion $record) {
                        $isCorrect = $record->attempt_answers()->where('is_correct', 1)->first();
                        $isSelected = $record->attempt_answers()->where('is_selected', 1)->first();
                        if ($isCorrect == $isSelected) {
                            return 1;
                        }
                        return 0;
                    })
                    ->label(__('monitoring.quizzes.quiz.score'))->html()->searchable(),
            ])
            ->defaultPaginationPageOption(10);
    }

}
