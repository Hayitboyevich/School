<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolResource\Pages;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $slug = 'schools/schools';

    public static function getModelLabel(): string
    {
        return __('models/school.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/school.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label(__('models/school.prop.number'))
                    ->numeric()
                    ->requiredWithout('name')
                    ->minValue(1)
                    ->integer()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->label(__('models/school.prop.name'))
                    ->requiredWithout('number')
                    ->minLength(3)
                    ->maxLength(50)
                    ->columnSpanFull(),
                Forms\Components\Select::make('city_id')
                    ->label(__('models/school.relation.cities'))
                    ->relationship('city', 'name')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label(__('models/school.prop.number')),
                Tables\Columns\TextColumn::make('name')->label(__('models/school.prop.name')),
                Tables\Columns\TextColumn::make('city.name')->label(__('models/school.relation.cities')),
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
            'index' => Pages\ManageSchools::route('/'),
        ];
    }
}
