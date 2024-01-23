<?php

namespace App\Filament\Resources\TestTypeResource\Pages;

use App\Filament\Resources\TestTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTestTypes extends ManageRecords
{
    protected static string $resource = TestTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
