<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use App\Filament\Resources\AcademicYearResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAcademicYears extends ManageRecords
{
    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make(__('Update'))->url(fn(): string => route('sehriyo.sync-academic-years')),
        ];
    }
}
