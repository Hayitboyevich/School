<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookUserStatus: string implements HasLabel
{
    case STARTED = 'started';
    case PAUSED = 'paused';
    case FINISHED = 'finished';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::STARTED => 'Started',
            self::PAUSED => 'Paused',
            self::FINISHED => 'Finished',
        };
    }
}
