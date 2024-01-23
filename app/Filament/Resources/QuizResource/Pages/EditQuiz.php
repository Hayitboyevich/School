<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Table;

class EditQuiz extends EditRecord
{
    protected static string $resource = QuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            QuizResource\Widgets\QuestionsOverview::make()
        ];
    }
}
