<?php

namespace App\Filament\Pages\Monitoring\Quizzes\Tables;

use App\Filament\Pages\Monitoring\Quizzes\QuizMonitoring;
use App\Models\Quiz;
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

class QuizzesTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function render()
    {
        return view('filament.pages.monitoring.quizzes.tables.quizzes-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Quiz::query()
                ->select(['quizzes.id', 'name', 'status', 'created_at', 'end_date']))
            ->columns([
                Columns\TextColumn::make('name')->label(__('monitoring.quizzes.quiz.quiz_name'))->sortable(['name'])->searchable(),
                Columns\TextColumn::make('question')->state(fn($record) => $record->questions->count())
                    ->label(__('monitoring.quizzes.quiz.question'))->sortable(['question']),
                Columns\TextColumn::make('status')->label(__('monitoring.quizzes.quiz.status'))->sortable(),
                Columns\TextColumn::make('group_or_student')
                    ->state(function ($record) {
                        if ($record->groups->isEmpty()) {
                            return $record->users->pluck('name')->implode(', ') ?? '';
                        } else {
                            return $record->groups->pluck('name')->implode(', ');
                        }
                    })
                    ->label(__('monitoring.quizzes.quiz.class_or_student')),
                Columns\TextColumn::make('check_mode')->label(__('monitoring.quizzes.quiz.check_mode'))
                    ->state(function ($record) {
                        return __('Автоматический');
                    }),
                Columns\TextColumn::make('subject')
                    ->state(function ($record) {
                        if ($record->subjects->isEmpty()) {
                            return '';
                        } else {
                            return $record->subjects->count();
                        }
                    })
                    ->label(__('monitoring.quizzes.quiz.subject')),
                Columns\TextColumn::make('created_at')
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('d MMM Y'))
                    ->label(__('monitoring.quizzes.quiz.start_date'))
                    ->sortable(),
                Columns\TextColumn::make('end_date')
                    ->formatStateUsing(fn(string $state): string => Carbon::parse($state)->locale('ru')->isoFormat('d MMM Y'))
                    ->label(__('monitoring.quizzes.quiz.end_date'))->sortable(),
            ])
            ->recordUrl(fn(Model $record) => QuizMonitoring::getUrl(['recordId' => $record->id]))
            ->defaultPaginationPageOption(10);
    }
}
