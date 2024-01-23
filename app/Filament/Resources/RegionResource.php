<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $slug = 'geography/regions';

    public static function getModelLabel(): string
    {
        return __('models/region.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/region.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_id',)
                    ->label(__('models/region.relation.countries'))
                    ->relationship('country', 'name')->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->label(__('models/region.prop.name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minLength(3)
                    ->maxLength(50)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('models/region.prop.name')),
                Tables\Columns\TextColumn::make('country.name')->label(__('models/region.relation.countries')),
            ])
            ->filters([
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
            'index' => Pages\ManageRegions::route('/'),
        ];
    }
}
