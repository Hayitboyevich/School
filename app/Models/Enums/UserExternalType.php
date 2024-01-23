<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserExternalType: string implements HasLabel
{
    case EMPLOYEE = 'employee';
    case STUDENT = 'student';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EMPLOYEE => 'Employee',
            self::STUDENT => 'Student',
        };
    }
}
