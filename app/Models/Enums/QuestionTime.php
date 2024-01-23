<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuestionTime: int implements HasLabel
{
    case OneMinute = 1;
    case TwoMinutes = 2;
    case ThreeMinutes = 3;
    case FourMinutes = 4;
    case FiveMinutes = 5;
    case SixMinutes = 6;
    case SevenMinutes = 7;
    case EightMinutes = 8;
    case NineMinutes = 9;
    case TenMinutes = 10;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OneMinute => '1 минута',
            self::TwoMinutes => '2 минуты',
            self::ThreeMinutes => '3 минуты',
            self::FourMinutes => '4 минуты',
            self::FiveMinutes => '5 минут',
            self::SixMinutes => '6 минут',
            self::SevenMinutes => '7 минут',
            self::EightMinutes => '8 минут',
            self::NineMinutes => '9 минут',
            self::TenMinutes => '10 минут',
        };
    }
}
