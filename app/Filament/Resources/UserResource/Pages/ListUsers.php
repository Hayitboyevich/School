<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Student' => Tab::make()
                ->label(__('models/user.prop.student'))
                ->modifyQueryUsing(function ($query) {
                    $query->join('role_user', 'role_user.user_id', 'users.id')
                        ->join('roles', 'roles.id', 'role_user.role_id')
                        ->where('roles.name', 'student');
                }),
            'Employee' => Tab::make()
                ->label(__('models/user.prop.employee'))
                ->modifyQueryUsing(function ($query) {
                    $query->join('role_user', 'role_user.user_id', 'users.id')
                        ->join('roles', 'roles.id', 'role_user.role_id')
                        ->where('roles.name', 'teacher');
                }),
            'All' => Tab::make()
                ->label(__('models/user.prop.all'))
        ];
    }
}
