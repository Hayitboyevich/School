<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectionResource\Pages;
use App\Models\Direction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class DirectionResource extends Resource
{
    protected static ?string $model = Direction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $slug = 'schools/directions';

    public static function getModelLabel(): string
    {
        return __('models/direction.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/direction.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label(__('models/direction.prop.label'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->minLength(2)
                    ->maxLength(50)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->label(__('models/direction.prop.label'))
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDirections::route('/'),
        ];
    }
}
