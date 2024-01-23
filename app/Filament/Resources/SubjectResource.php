<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Direction;
use App\Models\Enums\SubjectType;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $slug = 'schools/subjects';

    public static function getModelLabel(): string
    {
        return __('models/subject.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/subject.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('models/subject.prop.name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minLength(3)
                    ->maxLength(50)
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->label(__('models/subject.prop.type'))
                    ->required()
                    ->options(SubjectType::class)
                    ->default(SubjectType::ORDINARY),
                Forms\Components\Select::make('direction')
                    ->label(__('models/subject.relation.directions'))
                    ->relationship('direction', 'label')
                    ->default(Direction::first()->id ?? 0)
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('models/subject.prop.name')),
                Tables\Columns\TextColumn::make('type')->label(__('models/subject.prop.type')),
                Tables\Columns\TextColumn::make('direction.label')->label(__('models/subject.relation.directions'))
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
            'index' => Pages\ManageSubjects::route('/'),
        ];
    }
}
