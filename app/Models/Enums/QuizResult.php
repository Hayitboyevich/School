<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuizResult: string implements HasLabel
{
    case DURING_QUIZ = 'DURING_QUIZ';
    case AFTER_QUIZ = 'AFTER_QUIZ';
    case QUIZ_DEADLINE = 'QUIZ_DEADLINE';
    case NEVER = 'NEVER';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DURING_QUIZ => 'во время теста',
            self::AFTER_QUIZ => 'после окончания теста',
            self::QUIZ_DEADLINE => 'после даты окончания проведения теста',
            self::NEVER => 'никогда',
        };
    }
}
