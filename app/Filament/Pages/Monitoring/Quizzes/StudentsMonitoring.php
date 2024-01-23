<?php

namespace App\Filament\Pages\Monitoring\Quizzes;

use App\Filament\Pages\Monitoring\Quizzes\Tables\StudentsTable;
use App\Infolists\Components\TableEntry;
use App\Models\Group;
use App\Models\Quiz;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class StudentsMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.monitoring.quizzes.students-monitoring';

    protected static ?string $slug = 'monitoring/quizzes/{recordId}/group/{groupId}/students';

    public function getTitle(): string|Htmlable
    {
        return 'Результаты теста  "' . $this->quiz->name . ' "  -   "' . $this->group->name . '"';
    }

    public Group $group;

    public Quiz $quiz;

    public function mount($recordId, $groupId)
    {
        $this->group = Group::findOrFail($groupId);
        $this->quiz = Quiz::findOrFail($recordId);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TableEntry::make('')
                ->state([
                    'table' => StudentsTable::class,
                    'params' => ['group' => $this->group, 'quiz' => $this->quiz]
                ])
        ]);
    }
}
