<?php

namespace App\Filament\Resources\GroupTypeResource\Pages;

use App\Filament\Resources\GroupTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGroupTypes extends ManageRecords
{
    protected static string $resource = GroupTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
