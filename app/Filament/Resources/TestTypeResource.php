<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestTypeResource\Pages;
use App\Models\TestType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class TestTypeResource extends Resource
{
    protected static ?string $model = TestType::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $slug = 'quizzes/test-types';

    public static function getModelLabel(): string
    {
        return __('models/test_type.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/test_type.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label(__('models/test_type.prop.label'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minLength(3)
                    ->maxLength(50)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->label(__('models/test_type.prop.label'))
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
            'index' => Pages\ManageTestTypes::route('/'),
        ];
    }
}
