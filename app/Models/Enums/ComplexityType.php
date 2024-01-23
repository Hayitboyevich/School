<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum ComplexityType: int implements HasLabel
{
    case DIFFICULT = 0;
    case EASY = 1;
    case  AVERAGE = 2;
    case  VERY_COMPLICATED = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DIFFICULT => 'Сложный',
            self::EASY => 'Легкий',
            self::AVERAGE => 'Средный',
            self::VERY_COMPLICATED => 'Очень сложный',
        };
    }
}
