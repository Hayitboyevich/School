<?php

namespace App\Filament\Pages\Monitoring\Groups;

use App\Filament\Pages\Monitoring\Groups\Tables\GroupsTable;
use App\Infolists\Components\TableEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class GroupsMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static string $view = 'filament.pages.monitoring.groups.groups-monitoring';

    protected static ?string $slug = 'monitoring/groups';

    public static function getNavigationLabel(): string
    {
        return __('models/group.navigation_name');
    }

    public function getTitle(): string|Htmlable
    {
        return __('models/group.header_name');
    }

    protected function getHeaderWidgets(): array
    {
        return [
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TableEntry::make('')
                ->state([
                    'table' => GroupsTable::class,
                    'params' => []
                ])
        ]);
    }
}
