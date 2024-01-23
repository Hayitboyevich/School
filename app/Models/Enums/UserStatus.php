<?php

namespace App\Models\Enums;
use Filament\Support\Contracts\HasLabel;
enum UserStatus: int implements HasLabel
{
    case GRADUATE = 3;
    case EXTERNAL = 2;
    case ACTIVE = 1;
    case DISABLED = 0;
    case REJECTED = -1;
    case MODERATION = -2;
    case DROPPED_OUT = -3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GRADUATE => 'Выпускник',
            self::EXTERNAL => 'Внешний',
            self::ACTIVE => 'Активный',
            self::DISABLED => 'Отключен',
            self::REJECTED => 'Отклонён',
            self::MODERATION => 'На модерации',
            self::DROPPED_OUT => 'Выбыл ',
        };
    }
}
