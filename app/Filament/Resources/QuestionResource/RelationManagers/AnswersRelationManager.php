<?php

namespace App\Filament\Resources\QuestionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    protected static ?string $recordTitleAttribute = 'content';

    public static function getModelLabel(): ?string
    {
        return __('models/answer.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/answer.plural');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('answers')
                    ->label(__('models/answer.prop.content'))
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_correct')
                    ->label(__('models/answer.prop.is_correct'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')->label(__('models/answer.prop.content'))->html(),
                Tables\Columns\ToggleColumn::make('is_correct')->label(__('models/answer.prop.is_correct')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
