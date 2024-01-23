<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;
use function Laravel\Prompts\search;

enum QuizAccess: string implements HasLabel
{
    case PRIVATE = 'private';
    case PUBLIC = 'public';

    public static function valueOf($value): ?QuizAccess
    {
        if ($value === null) return null;
        if ($value instanceof self) return $value;
        foreach (self::cases() as $case) {
            if ($case->value === $value) return $case;
        }
        return null;
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PRIVATE => 'Для зарегистрованных пользователей',
            self::PUBLIC => 'Публичный',
        };
    }
}
