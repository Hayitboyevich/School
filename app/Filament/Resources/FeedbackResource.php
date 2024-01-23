<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Enums\FeedbackType;
use App\Models\Feedback;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $slug = 'others/feedback';

    public static function getModelLabel(): string
    {
        return __('models/feedback.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/feedback.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label(__('models/feedback.prop.type'))
                    ->options(FeedbackType::class)
                    ->required()
                    ->columnSpanFull()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('record_id', null)),
                Forms\Components\Hidden::make('record_id')
                    ->hidden(fn(callable $get) => FeedbackType::tryFrom($get('type')) != FeedbackType::NEW_BOOK),
                Forms\Components\Select::make('record_id')
                    ->label(__('models/feedback.prop.record_id'))
                    ->required()
                    ->options(function (callable $get) {
                        $type = FeedbackType::tryFrom($get('type'));
                        switch ($type) {
                            case FeedbackType::BOOK:
                                return Book::all()->pluck('name', 'id')->toArray();
                            case FeedbackType::BOOK_CHAPTER:
                                return BookChapter::all()->pluck('name', 'id')->toArray();
                            case FeedbackType::QUIZ:
                                return Quiz::all()->pluck('name', 'id')->toArray();
                        }
                    })
                    ->searchable()
                    ->hidden(fn(callable $get) => FeedbackType::tryFrom($get('type')) == FeedbackType::NEW_BOOK)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('content')
                    ->label(__('models/feedback.prop.content'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->label(__('models/feedback.prop.type')),
                Tables\Columns\TextColumn::make('record.name')->label(__('models/feedback.prop.record_id')),
                Tables\Columns\TextColumn::make('content')->label(__('models/feedback.prop.content')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFeedback::route('/'),
        ];
    }
}
