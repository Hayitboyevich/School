<?php

namespace App\Filament\Resources\ReaderRankResource\Pages;

use App\Filament\Resources\ReaderRankResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageReaderRanks extends ManageRecords
{
    protected static string $resource = ReaderRankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
