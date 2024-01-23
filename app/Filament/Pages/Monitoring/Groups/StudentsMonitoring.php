<?php

namespace App\Filament\Pages\Monitoring\Groups;

use App\Filament\Pages\Monitoring\Groups\Tables\BookStudentsTable;
use App\Filament\Pages\Monitoring\Groups\Tables\BookTable;
use App\Filament\Pages\Monitoring\Groups\Widgets\BookStates;
use App\Infolists\Components\TableEntry;
use App\Models\Book;
use App\Models\Group;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class StudentsMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static string $view = 'filament.pages.monitoring.groups.students-monitoring';

    protected static ?string $slug = 'monitoring/groups/{groupId}/book/{bookId}';

    public function getTitle(): string|Htmlable
    {
        return 'Книга - "' . $this->book->name . '"' . "  - " . $this->group->name;
    }

    public Book $book;
    public Group $group;

    public function mount($groupId, $bookId)
    {
        $this->group = Group::findOrFail($groupId);
        $this->book = Book::findOrFail($bookId);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BookStates::make(['group' => $this->group, 'book' => $this->book])
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TableEntry::make('')
                ->state([
                    'table' => BookTable::class,
                    'params' => ['book' => $this->book, 'group' => $this->group]
                ])
        ]);
    }
}
