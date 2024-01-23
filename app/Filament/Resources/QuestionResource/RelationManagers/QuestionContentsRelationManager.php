<?php

namespace App\Filament\Resources\QuestionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class QuestionContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'question_contents';

    protected static ?string $recordTitleAttribute = 'value';

    protected static ?string $label = 'question content';

    public static function getModelLabel(): ?string
    {
        return __('models/question_content.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/question_content.plural');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('value')
                    ->label(__('models/question_content.prop.value'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('value')->label(__('models/question_content.prop.value'))->html(),
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
