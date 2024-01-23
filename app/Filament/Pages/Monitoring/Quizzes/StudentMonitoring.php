<?php

namespace App\Filament\Pages\Monitoring\Quizzes;

use App\Filament\Pages\Monitoring\Quizzes\Tables\StudentTable;
use App\Infolists\Components\TableEntry;
use App\Models\Group;
use App\Models\Quiz;
use App\Models\User;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class StudentMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.monitoring.quizzes.student-monitoring';

    protected static ?string $slug = 'monitoring/quizzes/{quizId}/group/{groupId}/student/{userId}';

    public function getTitle(): string|Htmlable
    {
        return $this->user->name . '  -   "' . $this->group->name . '" - Тест- "' . $this->quiz->name . '"';

    }


    public Group $group;

    public Quiz $quiz;

    public User $user;

    public function mount($quizId, $groupId, $userId)
    {
        $this->group = Group::findOrFail($groupId);
        $this->quiz = Quiz::findOrFail($quizId);
        $this->user = User::findOrFail($userId);

    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TableEntry::make('')
                ->state([
                    'table' => StudentTable::class,
                    'params' => ['group' => $this->group, 'quiz' => $this->quiz, 'user' => $this->user]
                ])
        ]);
    }
}
