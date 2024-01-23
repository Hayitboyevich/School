<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateQuiz extends CreateRecord
{
    protected static string $resource = QuizResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('models/quiz.create');
    }
}
