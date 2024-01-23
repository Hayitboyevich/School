<?php

namespace App\Filament\Resources\QuestionResource\RelationManagers;

use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class QuizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizzes';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): ?string
    {
        return __('models/quiz.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/quiz.plural');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('models/quiz.prop.name')),
                Tables\Columns\TextColumn::make('description')->label(__('models/quiz.prop.description'))->html(),
                Tables\Columns\TextColumn::make('subjects.name')->label(__('models/quiz.relation.subjects')),
                Tables\Columns\ToggleColumn::make('status')->label(__('models/quiz.prop.status'))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
