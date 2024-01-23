<?php

namespace App\Filament\Pages\Monitoring\Books;

use App\Filament\Pages\Monitoring\Books\Tables\ChaptersTable;
use App\Filament\Pages\Monitoring\Books\Tables\GroupsTable;
use App\Filament\Pages\Monitoring\Books\Widgets\BookStats;
use App\Infolists\Components\TableEntry;
use App\Models\Book;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class BookMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.monitoring.books.book-monitoring';

    protected static ?string $title = 'Классы';

    protected static ?string $slug = 'monitoring/books/{recordId}';

    public function getTitle(): string|Htmlable
    {
        return 'Книга "' . $this->book->name . '"';
    }

    public Book $book;

    public function mount($recordId)
    {
        $this->book = Book::findOrFail($recordId);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BookStats::make(['book' => $this->book]),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('tabs')
                ->tabs([
                    Tabs\Tab::make('groups')
                        ->label(__('monitoring.books.groups.title'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => GroupsTable::class,
                                    'params' => [
                                        'book' => $this->book
                                    ]
                                ])
                        ]),
                    Tabs\Tab::make('chapters')
                        ->label(__('monitoring.books.chapters.title'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => ChaptersTable::class,
                                    'params' => [
                                        'book' => $this->book
                                    ]
                                ])
                        ])
                ])
                ->contained(false)
        ]);
    }
}
