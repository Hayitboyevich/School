<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuizQuestionOrder: string implements HasLabel
{
    case RANDOM = 'random';
    case ORDERED = 'ordered';

    public static function valueOf($value): ?QuizQuestionOrder
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
            self::RANDOM => 'В перемешку',
            self::ORDERED => 'По очереди',
        };
    }
}

