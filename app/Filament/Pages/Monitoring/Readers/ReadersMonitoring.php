<?php

namespace App\Filament\Pages\Monitoring\Readers;

use App\Filament\Pages\Monitoring\Readers\Tables\FinishedReadersTable;
use App\Filament\Pages\Monitoring\Readers\Tables\PausedReadersTable;
use App\Filament\Pages\Monitoring\Readers\Tables\StartedReadersTable;
use App\Infolists\Components\TableEntry;
use App\Models\Book;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ReadersMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark-square';

    protected static string $view = 'filament.pages.monitoring.readers.readers-monitoring';

    protected static ?string $slug = 'monitoring/readers';

    public static function getNavigationLabel(): string
    {
        return __('monitoring.readers.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.readers.title');
    }

    public function getBreadcrumbs(): array
    {
        return [
            'readers' => __('monitoring.title'),
            __('monitoring.readers.title')
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('tabs')
                ->tabs([
                    Tabs\Tab::make('started')
                        ->label(__('monitoring.readers.tabs.started'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => StartedReadersTable::class,
                                    'params' => []
                                ])
                        ]),
                    Tabs\Tab::make('paused')
                        ->label(__('monitoring.readers.tabs.paused'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => PausedReadersTable::class,
                                    'params' => []
                                ])
                        ]),
                    Tabs\Tab::make('finished')
                        ->label(__('monitoring.readers.tabs.finished'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => FinishedReadersTable::class,
                                    'params' => []
                                ])
                        ]),
                ])
                ->contained(false)
                ->persistTabInQueryString()
        ]);
    }
}
