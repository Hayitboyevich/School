<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\Actions\CopyAction;
use App\Filament\Resources\QuizResource\Pages;
use App\Models\AcademicYear;
use App\Models\Enums\QuizPurpose;
use App\Models\Enums\QuestionTime;
use App\Models\Enums\QuizAccess;
use App\Models\Enums\QuizPortfolio;
use App\Models\Enums\QuizResult;
use App\Models\Enums\QuizQuestionOrder;
use App\Models\Group;
use App\Models\Quiz;
use App\Models\TestType;
use App\Models\User;
use App\Services\AcademicYearService;
use App\Services\BookChapterService;
use App\Services\BookService;
use App\Services\QuizService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $slug = 'quizzes/quizzes';

    public static function getModelLabel(): string
    {
        return __('models/quiz.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/quiz.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('content')
                            ->label(__('models/quiz.tabs.content'))
                            ->icon('heroicon-m-inbox')
                            ->schema([
                                Forms\Components\Section::make()->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label(__('models/quiz.prop.name'))
                                        ->required()
                                        ->minLength(2)
                                        ->maxLength(1000),
                                    Forms\Components\RichEditor::make('description')
                                        ->fileAttachmentsDisk('public')
                                        ->fileAttachmentsDirectory('quiz_description')
                                        ->label(__('models/quiz.prop.description')),
                                    Forms\Components\Radio::make('purpose')
                                        ->label(__('models/quiz.prop.purpose'))
                                        ->options(QuizPurpose::class)
                                        ->inline()
                                        ->inlineLabel(false)
                                        ->default(QuizPurpose::BY_SUBJECT)
                                        ->live()
                                        ->afterStateUpdated(function (?Model $record, Forms\Components\Field $component, $state) {
                                            $record->{$component->getName()} = $state;
                                            $record->save();
                                        })
                                        ->columnSpanFull(),
                                    Forms\Components\Select::make('subjects')
                                        ->label(__('models/quiz.relation.subjects'))
                                        ->relationship('subjects', 'name')
                                        ->multiple()
                                        ->hidden(fn(Forms\Get $get) => QuizPurpose::valueOf($get('purpose')) !== QuizPurpose::BY_SUBJECT)
                                        ->preload(),
                                    Forms\Components\Select::make('books')
                                        ->label(__('models/quiz.relation.books'))
                                        ->relationship('books', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->hidden(fn(Forms\Get $get) => QuizPurpose::valueOf($get('purpose')) !== QuizPurpose::BY_BOOK)
                                        ->afterStateUpdated(fn($state, callable $set, callable $get, BookChapterService $bookChapterService) => $set('book_chapters', $bookChapterService->filterOut($state, $get('book_chapters')))),
                                    Forms\Components\Select::make('book_chapters')
                                        ->label(__('models/quiz.relation.book_chapters'))
                                        ->relationship('book_chapters', 'name')
                                        ->options(fn(callable $get, BookService $bookService) => $bookService->getGroupedBookChapterOptions($get('books')))
                                        ->multiple()
                                        ->hidden(fn(Forms\Get $get) => QuizPurpose::valueOf($get('purpose')) !== QuizPurpose::BY_BOOK)
                                        ->preload(),
                                    Forms\Components\Toggle::make('status')
                                        ->label(__('models/quiz.prop.status'))
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->default(1),
                                ])->columnSpan(2),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('authors')
                                            ->label(__('models/quiz.prop.quiz_authors'))
                                            ->relationship('authors', 'id')
                                            ->getOptionLabelFromRecordUsing(fn(User $record) => nameShortened($record->name))
                                            ->searchable()
                                            ->default([Auth::id()])
                                            ->preload(),
                                        Forms\Components\DateTimePicker::make('created_at')
                                            ->label(__('models/quiz.prop.created_at'))
                                            ->seconds(false)
                                            ->displayFormat('d.m.Y H:i')
                                            ->native(false)
                                            ->disabled()
                                            ->closeOnDateSelection(),
                                        Forms\Components\DateTimePicker::make('updated_at')
                                            ->label(__('models/quiz.prop.updated_at'))
                                            ->seconds(false)
                                            ->displayFormat('d.m.Y H:i')
                                            ->native(false)
                                            ->disabled()
                                            ->closeOnDateSelection(),
                                        Forms\Components\DateTimePicker::make('start_date')
                                            ->label(__('models/quiz.prop.start_date'))
                                            ->seconds(false)
                                            ->native(false)
                                            ->displayFormat('d.m.Y H:i')
                                            ->closeOnDateSelection()
                                            ->columnSpanFull(),
                                        Forms\Components\DateTimePicker::make('end_date')
                                            ->label(__('models/quiz.prop.end_date'))
                                            ->seconds(false)
                                            ->displayFormat('d.m.Y H:i')
                                            ->native(false)
                                            ->closeOnDateSelection(),
                                        Forms\Components\TextInput::make('question_count')
                                            ->label(__('models/quiz.prop.question_count'))
                                            ->numeric(),
                                        Forms\Components\TextInput::make('duration')
                                            ->label(__('models/quiz.prop.duration'))
                                            ->numeric(),
                                    ])->columnSpan(1)
                            ])
                            ->columns(3),
                        Forms\Components\Tabs\Tab::make('settings')
                            ->label(__('models/quiz.tabs.setting'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Select::make('question_order')
                                    ->label(__('models/quiz.relation.question_order'))
                                    ->preload()
                                    ->options(QuizQuestionOrder::class)
                                    ->default(QuizQuestionOrder::RANDOM)
                                    ->required()
                                    ->selectablePlaceholder(false)
                                    ->columnSpanFull(),
                                Forms\Components\Radio::make('for_portfolio')
                                    ->label(__('models/quiz.prop.for_portfolio'))
                                    ->options(QuizPortfolio::class)
                                    ->default(QuizPortfolio::NO)
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('academic_years')
                                    ->label(__('models/quiz.relation.academic_years'))
                                    ->relationship('academic_years', 'start')
                                    ->getOptionLabelFromRecordUsing(fn(AcademicYear $record) => $record->period)
                                    ->default(fn(AcademicYearService $academicYearService) => [$academicYearService->getDefault()->id])
                                    ->multiple()
                                    ->preload(),
                                Forms\Components\Radio::make('result_show')
                                    ->label(__('models/quiz.prop.result_show'))
                                    ->boolean()
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->default(QuizResult::AFTER_QUIZ)
                                    ->options(QuizResult::class)
                                    ->columnSpanFull(),
                                Forms\Components\Radio::make('access')
                                    ->label(__('models/quiz.prop.access'))
                                    ->options(QuizAccess::class)
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set, $state, ?Model $record, QuizService $quizService) => $quizService->generatePublicLink($set, 'link', $state, $record))
                                    ->default(QuizAccess::PRIVATE)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('link')
                                    ->label(__('models/quiz.prop.link'))
                                    ->required()
                                    ->readOnly()
                                    ->prefix(route('quizzes.index') . '/')
                                    ->visible(fn(Forms\Get $get) => QuizAccess::valueOf($get('access')) == QuizAccess::PUBLIC)
                                    ->suffixActions([CopyAction::make()->copyable(fn(Get $get) => route('quizzes.index') . '/' . $get('link'))])
                                    ->columnSpanFull(),
                                Forms\Components\Hidden::make('link'),
                                Forms\Components\Select::make('question_time')
                                    ->label(__('models/quiz.prop.question_time'))
                                    ->options(QuestionTime::class)
                                    ->default(QuestionTime::TwoMinutes)
                                    ->preload()
                                    ->selectablePlaceholder(false)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('score')
                                    ->label(__('models/quiz.prop.score'))
                                    ->placeholder('1.0')
                                    ->numeric()
                                    ->default(1)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('retakes')
                                    ->label(__('models/quiz.prop.retakes'))
                                    ->options(range(0, 10))
                                    ->preload()
                                    ->selectablePlaceholder()
                                    ->default('0')
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('test_type_id')
                                    ->label(__('models/quiz.prop.test_type'))
                                    ->relationship('test_type')
                                    ->getOptionLabelFromRecordUsing(fn(TestType $record) => $record->label)
                                    ->preload()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('groups')
                                    ->label(__('models/quiz.prop.group_level'))
                                    ->relationship('groups', 'id')
                                    ->multiple()
                                    ->options(fn(AcademicYearService $academicYearService, Group $groups) => $groups
                                        ->whereJsonContains('academic_year_ids', $academicYearService->getDefault()->external_id)
                                        ->orderBy('group_level', 'asc')
                                        ->orderBy('group_letter', 'asc')
                                        ->get()
                                        ->pluck('name', 'id')
                                    )
                                    ->getOptionLabelFromRecordUsing(fn(Group $record) => $record->name)
                                    ->preload()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('users')
                                    ->label(__('models/quiz.relation.users'))
                                    ->relationship('users', 'id')
                                    ->searchable()
                                    ->getOptionLabelFromRecordUsing(function (User $record, AcademicYearService $academicYearService) {
                                        $academic_year_id = $academicYearService->getDefault()->external_id;
                                        $groups = $record->groups
                                            ->filter(fn($group) => in_array($academic_year_id, $group->academic_year_ids ?? []))
                                            ->pluck('name')->implode(' ');
                                        return "$record->name $groups";
                                    })
                                    ->multiple()
                                    ->preload()
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('models/quiz.prop.name')),
                Tables\Columns\TextColumn::make('description')->label(__('models/quiz.prop.description'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
