<?php

namespace App\Filament\Forms\Components\Actions;

use App\Filament\Forms\Components\Actions\Concerns\HasCopyable;
use Filament\Forms\Components\Actions\Action as BaseAction;

class CopyAction extends BaseAction
{
    use HasCopyable;
}
