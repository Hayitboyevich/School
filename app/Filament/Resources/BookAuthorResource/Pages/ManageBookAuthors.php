<?php

namespace App\Filament\Resources\BookAuthorResource\Pages;

use App\Filament\Resources\BookAuthorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBookAuthors extends ManageRecords
{
    protected static string $resource = BookAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
