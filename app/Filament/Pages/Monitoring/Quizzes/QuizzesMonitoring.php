<?php

namespace App\Filament\Pages\Monitoring\Quizzes;

use App\Filament\Pages\Monitoring\Quizzes\Tables\QuizzesTable;
use App\Filament\Pages\Monitoring\Quizzes\Widgets\QuizStats;
use App\Infolists\Components\TableEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists\Components\Tabs;

class QuizzesMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static string $view = 'filament.pages.monitoring.quizzes.quizzes-monitoring';

    protected static ?string $slug = 'monitoring/quizzes';

    public static function getNavigationLabel(): string
    {
        return __('monitoring.quizzes.navigation_name');
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.quizzes.prop.quizzes');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            QuizStats::class,
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('tabs')
                ->tabs([
                    Tabs\Tab::make('schools')
                        ->label(__('monitoring.quizzes.prop.school'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => QuizzesTable::class,
                                    'params' => []
                                ])
                        ]),
                    Tabs\Tab::make('chapters')
                        ->label(__('monitoring.quizzes.prop.quizzes_public'))
                        ->schema([
                            TableEntry::make('')
                                ->state([
                                    'table' => QuizzesTable::class,
                                    'params' => []
                                ])
                        ])
                ])
                ->contained(false)
        ]);
    }
}

