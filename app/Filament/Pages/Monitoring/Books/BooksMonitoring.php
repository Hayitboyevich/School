<?php

namespace App\Filament\Pages\Monitoring\Books;

use App\Filament\Pages\Monitoring\Books\Tables\BooksTable;
use App\Filament\Pages\Monitoring\Books\Widgets\BooksStats;
use App\Infolists\Components\TableEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class BooksMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static string $view = 'filament.pages.monitoring.books.books-monitoring';

    protected static ?string $slug = 'monitoring/books';

    public static function getNavigationLabel(): string
    {
        return __('monitoring.books.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.books.reading_monitoring');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BooksStats::class
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TableEntry::make('')
                ->state([
                    'table' => BooksTable::class,
                    'params' => []
                ])
        ]);
    }
}
