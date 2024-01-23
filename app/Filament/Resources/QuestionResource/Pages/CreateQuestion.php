<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('models/question.action.create');
    }
}
