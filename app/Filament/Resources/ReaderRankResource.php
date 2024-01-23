<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReaderRankResource\Pages;
use App\Models\ReaderRank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class ReaderRankResource extends Resource
{
    protected static ?string $model = ReaderRank::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $slug = 'others/reader-ranks';

    public static function getModelLabel(): string
    {
        return __('models/reader_rank.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/reader_rank.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label(__('models/reader_rank.prop.label'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minLength(2)
                    ->maxLength(50)
                    ->columnSpanFull()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->label(__('models/reader_rank.prop.label'))
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
            'index' => Pages\ManageReaderRanks::route('/'),
        ];
    }
}
