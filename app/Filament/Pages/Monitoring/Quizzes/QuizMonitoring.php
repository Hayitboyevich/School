<?php

namespace App\Filament\Pages\Monitoring\Quizzes;

use App\Filament\Pages\Monitoring\Quizzes\Tables\GroupsTable;
use App\Filament\Pages\Monitoring\Quizzes\Tables\QuizzesTable;
use App\Filament\Pages\Monitoring\Quizzes\Tables\StudentsTable;
use App\Filament\Pages\Monitoring\Quizzes\Widgets\GroupState;
use App\Filament\Pages\Monitoring\Quizzes\Widgets\QuizStats;
use App\Infolists\Components\TableEntry;
use App\Models\Quiz;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists\Components\Tabs;

class QuizMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static string $view = 'filament.pages.monitoring.quizzes.quiz-monitoring';

    protected static ?string $slug = 'monitoring/quizzes/{recordId}/groups';

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.quizzes.prop.groups');
    }

    public Quiz $quiz;

    public function mount($recordId)
    {
        $this->quiz = Quiz::findOrFail($recordId);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('tabs')
                ->tabs([
                    Tabs\Tab::make('groups')
                        ->label(__('monitoring.quizzes.prop.all'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => GroupsTable::class,
                                    'params' => ['quiz' => $this->quiz]
                                ])
                        ]),
                    Tabs\Tab::make('primary_classes')
                        ->label(__('monitoring.quizzes.prop.primary_classes'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => GroupsTable::class,
                                    'params' => []
                                ])
                        ])
                ])
                ->contained(false)
        ]);
    }
}

