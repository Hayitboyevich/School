<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use App\Services\RoleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $slug = 'users/roles';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('models/role.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/role.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label(__('models/role.prop.label'))
                    ->required()
                    ->minLength(3)
                    ->maxLength(50)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->label(__('models/role.prop.name'))
                    ->required()
                    ->minLength(3)
                    ->maxLength(50)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->label(__('models/role.prop.label')),
                Tables\Columns\TextColumn::make('name')->label(__('models/role.prop.name'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(fn($record, RoleService $roleService) => $roleService->delete($record))
                    ->failureNotification(
                        Notification::make()
                            ->warning()
                            ->title('Роль не удалена!')
                            ->body('Эту роль нельзя удалить.'))
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->using(fn($records, RoleService $roleService) => $roleService->delete(...$records)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}
