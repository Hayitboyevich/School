<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $slug = 'schools/groups';

    public static function getModelLabel(): string
    {
        return __('models/group.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/group.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('group_id')
                    ->numeric(),
                Forms\Components\TextInput::make('group_level')
                    ->numeric(),
                Forms\Components\TextInput::make('group_letter')
                    ->maxLength(255),
                Forms\Components\Select::make('academic_year_ids')
                    ->multiple(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_id')
                    ->label(__('models/group.prop.group_id'))
                    ->description(fn($record) => $record->id)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group_level')
                    ->label(__('models/group.prop.group_level'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group_letter')
                    ->label(__('models/group.prop.group_letter'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('academic_year')
                    ->label(__('models/group.prop.academic_year_ids'))
                    ->getStateUsing(fn(Model $record) => $record->academic_years->pluck('period'))
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGroups::route('/'),
        ];
    }
}
