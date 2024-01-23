<?php

namespace App\Filament\Pages\Monitoring\Groups;

use App\Filament\Pages\Monitoring\Groups\Tables\BooksTable;
use App\Filament\Pages\Monitoring\Groups\Tables\ChaptersTable;
use App\Filament\Pages\Monitoring\Groups\Tables\StudentsTable;
use App\Filament\Pages\Monitoring\Groups\Widgets\GroupsStates;
use App\Infolists\Components\TableEntry;
use App\Models\Group;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class GroupMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.monitoring.groups.group-monitoring';

    //protected static ?string $title = 'Классы';

    protected static ?string $slug = 'monitoring/groups/{recordId}';

    public function getTitle(): string|Htmlable
    {
        return 'Класс "' . $this->group->name . '"';
    }

    public Group $group;

   public function mount($recordId)
    {
        $this->group = Group::findOrFail($recordId);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GroupsStates::make(['group' => $this->group])
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('tabs')
                ->tabs([
                    Tabs\Tab::make('books')
                        ->label(__('monitoring.groups.books.title'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => BooksTable::class,
                                    'params' => [
                                        'group' => $this->group
                                    ]
                                ])
                        ]),
                    Tabs\Tab::make('students')
                        ->label(__('monitoring.groups.students.title'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => StudentsTable::class,
                                    'params' => [
                                        'group' => $this->group
                                    ]
                                ])
                        ]),
                    Tabs\Tab::make('chapters')
                        ->label(__('monitoring.groups.chapters.title'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => ChaptersTable::class,
                                    'params' => [
                                        'group' => $this->group
                                    ]
                                ])
                        ]),

                ])
                ->contained(false)
        ]);
    }
}
