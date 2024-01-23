<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookAuthorResource\Pages;
use App\Models\BookAuthor;
use App\Rules\UniqueCombined;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class BookAuthorResource extends Resource
{
    protected static ?string $model = BookAuthor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $slug = 'books/book-authors';

    public static function getModelLabel(): string
    {
        return __('models/book_author.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/book_author.plural');
    }

    public static function form(Form $form): Form
    {
        $full_name_rule = fn($record, $get) => new UniqueCombined(
            'book_authors',
            ['first_name', 'last_name', 'middle_name'],
            $get,
            $record?->id,
            'full name'
        );

        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label(__('models/book_author.prop.first_name'))
                    ->required()
                    ->minLength(2)
                    ->maxLength(50)
                    ->columnSpanFull()
                    ->rules([$full_name_rule]),
                Forms\Components\TextInput::make('last_name')
                    ->label(__('models/book_author.prop.last_name'))
                    ->minLength(2)
                    ->maxLength(50)
                    ->columnSpanFull()
                    ->rules([$full_name_rule]),
                Forms\Components\TextInput::make('middle_name')
                    ->label(__('models/book_author.prop.middle_name'))
                    ->maxLength(50)
                    ->columnSpanFull()
                    ->rules([$full_name_rule]),
                Forms\Components\DatePicker::make('birth_date')
                    ->label(__('models/book_author.prop.birth_date'))
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label(__('models/book_author.prop.first_name')),
                Tables\Columns\TextColumn::make('last_name')->label(__('models/book_author.prop.last_name')),
                Tables\Columns\TextColumn::make('middle_name')->label(__('models/book_author.prop.middle_name')),
                Tables\Columns\TextColumn::make('birth_date')->label(__('models/book_author.prop.birth_date'))->dateTime('d/m/Y'),
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
            'index' => Pages\ManageBookAuthors::route('/'),
        ];
    }
}
