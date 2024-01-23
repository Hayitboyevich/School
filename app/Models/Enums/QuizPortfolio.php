<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuizPortfolio: int implements HasLabel
{
    case YES = 1;
    case NO = 0;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::YES => 'Да',
            self::NO => 'Нет',
        };
    }
}
