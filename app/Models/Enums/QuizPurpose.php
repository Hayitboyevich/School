<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuizPurpose: string implements HasLabel
{
    case BY_SUBJECT = 'BY_SUBJECT';
    case BY_BOOK = 'BY_BOOK';

    public static function valueOf($value): ?QuizPurpose
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
            self::BY_SUBJECT => 'По предмету',
            self::BY_BOOK => 'По книге',
        };
    }
}
