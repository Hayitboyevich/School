<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum GenderStatus: int implements HasLabel
{

    case MALE = 1;
    case FEMALE = 0;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MALE => 'мужской',
            self::FEMALE => 'женский',
        };
    }
}
