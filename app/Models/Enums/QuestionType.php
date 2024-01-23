<?php
namespace App\Models\Enums;
use Filament\Support\Contracts\HasLabel;
enum QuestionType: string implements HasLabel
{
    case SINGLE_CHOICE = 'SINGLE_CHOICE';
    case MULTIPLE_CHOICE = 'MULTIPLE_CHOICE';
    case ESSAY = 'ESSAY';
    case SHORT_ANSWER = 'SHORT_ANSWER';
    case MATH = 'MATH';

    public static function valueOf($value): ?QuestionType
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
            self::SINGLE_CHOICE => 'Одиночный выбор',
            self::MULTIPLE_CHOICE => 'Множественный выбор',
            self::ESSAY => 'Произвольный ввод',
            self::SHORT_ANSWER => 'Произвольный ввод с правильным ответом',
            self::MATH => 'Математический',
        };
    }
}
