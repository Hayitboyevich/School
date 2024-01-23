<?php

namespace App\Models\Enums;
use Filament\Support\Contracts\HasLabel;
enum SubjectType: int implements HasLabel
{
    case ORDINARY = 1;
    case CIRCLE = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ORDINARY => 'Обычный',
            self::CIRCLE => 'Кружок',
        };
    }
}
