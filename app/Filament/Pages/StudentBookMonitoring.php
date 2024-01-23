<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Monitoring\Groups\Tables\StudentBookOverview;
use App\Models\User;
use Filament\Pages\Page;
use App\Models\Group;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;

class StudentBookMonitoring extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.student-book-monitoring';

    protected static ?string $slug = 'monitoring/groups/{recordId}/students/{userId}';

    public User $user;
    public Group $group;

    public function mount($recordId, $userId)
    {
        $this->group = Group::findOrFail($recordId);
        $this->user = User::findOrFail($userId);
    }

    public function getWidgetData(): array
    {
        return [
            'user' => $this->user,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StudentBookOverview::make(['group' => $this->group, 'user' => $this->user]),
        ];
    }
}
