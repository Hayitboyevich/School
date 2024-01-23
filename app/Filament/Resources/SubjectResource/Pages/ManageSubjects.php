<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Resources\SubjectResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubjects extends ManageRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make(__('Update'))->url(fn(): string => route('sehriyo.sync-subjects')),
        ];
    }
}
