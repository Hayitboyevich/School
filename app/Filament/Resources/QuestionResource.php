<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Enums\QuestionType;
use App\Filament\Resources\QuestionResource\RelationManagers\QuizzesRelationManager;
use App\Models\Enums\QuizPurpose;
use App\Models\Question;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static ?string $slug = 'quizzes/questions';

    public static function getModelLabel(): string
    {
        return __('models/question.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/question.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Repeater::make('question_contents')
                            ->label('')
                            ->relationship('question_contents')
                            ->schema([
                                Forms\Components\RichEditor::make('value')
                                    ->label(__('models/question.singular'))
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('questions')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->required()
                            ->addActionLabel(__('models/question.action.add_question')),
                        Forms\Components\Repeater::make('Answer')
                            ->label(__('models/answer.singular'))
                            ->relationship('answers')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('answers')
                                    ->label(__('models/answer.singular'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_correct')
                                    ->label(__('models/answer.prop.is_correct'))
                                    ->required(),
                            ])
                            ->required()
                            ->hidden(fn(Get $get) => QuestionType::valueOf($get('type')) !== QuestionType::MULTIPLE_CHOICE)
                            ->addActionLabel(__('models/question.action.add_answer')),
                        Forms\Components\Repeater::make('Answer')
                            ->label(__('models/answer.singular'))
                            ->relationship('answers')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('answers')
                                    ->label(__('models/answer.singular'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_correct')
                                    ->label(__('models/answer.prop.is_correct'))
                                    ->required(),
                            ])
                            ->required()
                            ->hidden(fn(Get $get) => QuestionType::valueOf($get('type')) !== QuestionType::SINGLE_CHOICE)
                            ->addActionLabel(__('models/question.action.add_answer')),
                        Forms\Components\Repeater::make('Answer')
                            ->label(__('models/answer.singular'))
                            ->relationship('answers')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('answers')
                                    ->label(__('models/answer.singular'))
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_correct')
                                    ->label(__('models/answer.prop.is_correct'))
                                    ->hidden(fn(Get $get) => QuestionType::valueOf($get('type')) !== QuestionType::ESSAY)
                                    ->required(),
                            ])
                            ->required()
                            ->addable(false)
                            ->hidden(fn(Get $get) => QuestionType::valueOf($get('type')) !== QuestionType::ESSAY)
                            ->addActionLabel(__('models/question.action.add_answer')),
                        Forms\Components\Repeater::make('Answer')
                            ->label(__('models/answer.singular'))
                            ->relationship('answers')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('answers')
                                    ->label(__('models/answer.singular'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_correct')
                                    ->label(__('models/answer.prop.is_correct'))
                                    ->required(),
                            ])->required()
                            ->addable(false)
                            ->hidden(fn(Get $get) => QuestionType::valueOf($get('type')) !== QuestionType::SHORT_ANSWER)
                            ->addActionLabel(__('models/question.action.add_answer')),
                        Forms\Components\Repeater::make('Answer')
                            ->label(__('models/answer.singular'))
                            ->relationship('answers')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('answers')
                                    ->label(__('models/answer.singular'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('error_from')
                                    ->label(__('models/answer.prop.error_from'))
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('error_to')
                                    ->label(__('models/answer.prop.error_to'))
                                    ->columnSpanFull(),
                            ])
                            ->required()
                            ->addable(false)
                            ->hidden(fn(Get $get) => QuestionType::valueOf($get('type')) !== QuestionType::MATH)
                            ->addActionLabel(__('models/question.action.add_answer'))
                    ])
                    ->columnSpan(2),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label(__('models/question.prop.type'))
                            ->required()
                            ->options(QuestionType::class)
                            ->default(QuestionType::SINGLE_CHOICE)
                            ->live()
                            ->selectablePlaceholder(false)
                            ->columnSpanFull(),
                        Forms\Components\Radio::make('purpose')
                            ->label(__('models/question.prop.purpose'))
                            ->options(QuizPurpose::class)
                            ->default(QuizPurpose::BY_SUBJECT)
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('subjects')
                            ->label(__('models/question.relation.subjects'))
                            ->relationship('subjects', 'name')
                            ->multiple()
                            ->hidden(fn(Forms\Get $get) => QuizPurpose::valueOf($get('purpose')) !== QuizPurpose::BY_SUBJECT)
                            ->preload(),
                        Forms\Components\Select::make('books')
                            ->label(__('models/question.relation.books'))
                            ->relationship('books', 'name')
                            ->multiple()
                            ->preload()
                            ->hidden(fn(Forms\Get $get) => QuizPurpose::valueOf($get('purpose')) !== QuizPurpose::BY_BOOK),
                        Forms\Components\Select::make('group_level')
                            ->label(__('models/question.prop.group_level'))
                            ->multiple()
                            ->columnSpanFull()
                            ->options(
                                array_combine(
                                    range(1, 11),
                                    range(1, 11)
                                )
                            ),
                        Forms\Components\Select::make('authors')
                            ->label(__('models/question.prop.authors'))
                            ->relationship('authors', 'id')
                            ->getOptionLabelFromRecordUsing(fn(User $record) => nameShortened($record->name))
                            ->searchable()
                            ->multiple()
                            ->default([auth()->id()])
                            ->preload(),
                        Forms\Components\TextInput::make('created_at')
                            ->label(__('models/question.prop.created'))
                            ->formatStateUsing(fn($state) => human_date_with_time($state))
                            ->disabled(),
                        Forms\Components\TextInput::make('updated_at')
                            ->label(__('models/question.prop.updated'))
                            ->formatStateUsing(fn($state) => human_date_with_time($state))
                            ->disabled(),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question_contents.value')->label(__('models/question.prop.content'))->html(),
                Tables\Columns\TextColumn::make('type')->label(__('models/question.prop.type'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            QuizzesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
