<?php

namespace App\Filament\Resources\QuizResource\Widgets;

use App\Models\Enums\ComplexityType;
use App\Models\Enums\QuestionType;
use App\Models\Enums\QuizPurpose;
use App\Models\Question;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QuestionsOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function paginateTableQuery(Builder $query): LengthAwarePaginator
    {
        return $query->paginate((int)$this->getTableRecordsPerPage());
    }

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Вопросы';

    protected $listeners = ['updateActions' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->poll('1s')
            ->heading(__('models/quiz.question.adding_questions'))
            ->query(Question::query()
                ->select(['questions.*'])
                ->leftJoin('question_quiz', fn($query) => $query
                    ->on('question_quiz.question_id', 'questions.id')
                    ->where('question_quiz.quiz_id', $this->record->id)
                )
            )
            ->columns([
                Tables\Columns\TextColumn::make('row_number')->rowIndex()->label('№'),
                Tables\Columns\TextColumn::make('content')->label(__('models/quiz.question.title'))
                    ->html()->searchable(),
                Tables\Columns\TextColumn::make('type')->label(__('models/quiz.question.question_type')),
                Tables\Columns\TextColumn::make('subjects.name')->label(__('models/question.relation.subject'))
                    ->visible(fn() => QuizPurpose::valueOf($this->record->purpose) == QuizPurpose::BY_SUBJECT),
                Tables\Columns\TextColumn::make('books.name')->label(__('models/question.relation.books'))
                    ->visible(fn() => QuizPurpose::valueOf($this->record->purpose) == QuizPurpose::BY_BOOK),
                Tables\Columns\TextColumn::make('group_level')->label(__('models/question.prop.group_level')),
                Tables\Columns\TextInputColumn::make('time')->label(__('models/quiz.question.time')),
                Tables\Columns\TextInputColumn::make('weight')->label(__('models/quiz.question.weight')),
                Tables\Columns\TextColumn::make('complexitys')
                    ->default(function ($record) {
                        if ($record->complexity == ComplexityType::DIFFICULT) {
                            return 'Сложный';
                        } elseif ($record->complexity == ComplexityType::EASY) {
                            return 'Легкий';
                        } elseif ($record->complexity == ComplexityType::AVERAGE) {
                            return 'Средный';
                        } elseif ($record->complexity == ComplexityType::VERY_COMPLICATED) {
                            return 'Очень сложный';
                        }
                    })->label(__('models/quiz.question.complexity'))
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subjects')
                    ->label('Предмет')
                    ->relationship('subjects', 'name')
                    ->multiple()
                    ->visible(fn() => QuizPurpose::valueOf($this->record->purpose) == QuizPurpose::BY_SUBJECT)
                    ->preload()
                    ->columnSpan(1),
                Tables\Filters\SelectFilter::make('books')
                    ->label(__('models/quiz.question.books'))
                    ->relationship('quizzes.books', 'name')
                    ->multiple()
                    ->visible(fn() => QuizPurpose::valueOf($this->record->purpose) == QuizPurpose::BY_BOOK)
                    ->preload()
                    ->columnSpan(1),
                Tables\Filters\SelectFilter::make('group_level')
                    ->label('Класс')
                    ->multiple()
                    ->options(
                        array_combine(
                            range(1, 11),
                            range(1, 11)
                        )
                    )
                    ->preload()
                    ->query(fn($query, $data) => $query->whereJsonContains('group_level', $data['values']))
                    ->columnSpan(1),
                Tables\Filters\SelectFilter::make('complexity')
                    ->label(__('models/quiz.question.complexity'))
                    ->options(ComplexityType::class)
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->columnSpan(1),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип')
                    ->options(QuestionType::class)
                    ->preload()
                    ->multiple()
                    ->columnSpan(1),
                Tables\Filters\SelectFilter::make('teachers')
                    ->label('Учитель')
                    ->searchable()
                    ->options(function () {
                        return User::whereHas('roles', function ($query) {
                            $query->where('name', 'teacher');
                        })->get()->mapWithKeys(function ($user) {
                            return [$user->id => nameShortened($user->name)];
                        });
                    })
                    ->multiple()
                    ->preload()
                    ->columnSpan(1),
                Tables\Filters\Filter::make('attached')
                    ->label('Только прикрепленные вопросы')
                    ->default()
                    ->query(fn($query) => $query->whereNotNull('question_quiz.id'))
            ],
                Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(5)
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('attach')
                    ->label('Прикрепить')
                    ->icon('heroicon-o-link')
                    ->visible(fn($record) => !$this->record->questions->contains(fn($question) => $question->id == $record->id))
                    ->action(function (Question $record) {
                        $this->record->questions()->syncWithoutDetaching([$record->id]);
                        $this->dispatch('updateActions');
                    }),
                Tables\Actions\Action::make('detach')
                    ->label('Открепить')
                    ->icon('heroicon-o-x-mark')
                    ->color(Color::Red)
                    ->visible(fn($record) => $this->record->questions->contains(fn($question) => $question->id == $record->id))
                    ->action(function (Question $record) {
                        $this->record->questions()->detach([$record->id]);
                        $this->dispatch('updateActions');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
