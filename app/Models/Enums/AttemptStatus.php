<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum AttemptStatus: string implements HasLabel
{
    case STARTED = 'started';
    case FINISHED = 'finished';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::STARTED => 'Started',
            self::FINISHED => 'Finished',
        };
    }
}
