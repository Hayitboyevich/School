<?php

namespace App\Filament\Resources\BookResource\RelationManagers;

use App\Models\Group;
use App\Services\AcademicYearService;
use App\Services\BookChapterService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $title = 'Классы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('group_letter')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('group_letter')
            ->columns([
                Tables\Columns\TextColumn::make('group_level_and_letter')
                    ->label(__('models/book.relation.group'))
                    ->default(function ($record) {
                        return $record->group_level . ' ' . $record->group_letter;
                    })->searchable(),
                Tables\Columns\TextInputColumn::make('score')
                    ->label(__('models/book.relation.score'))
                    ->type('number')
                    ->updateStateUsing(fn($state, $record) => $this->updateBookGroup('score', $state, $record))
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('start_date')
                    ->label(__('models/group.prop.start_date'))
                    ->type('date')
                    ->updateStateUsing(fn($state, $record) => $this->updateBookGroup('start_date', $state, $record))
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('end_date')
                    ->label(__('models/group.prop.end_date'))
                    ->type('date')
                    ->updateStateUsing(fn($state, $record) => $this->updateBookGroup('end_date', $state, $record))
                    ->sortable()
            ])
            ->defaultSort(function ($query) {
                        $query->orderBy('group_level', 'asc')
                            ->orderBy('group_letter', 'asc');
                    })
            ->filters([
                Tables\Filters\Filter::make('start_date'),
                Tables\Filters\Filter::make('end_date'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('models/book.relation.add_group'))
                    ->color('warning')
                    ->recordTitle(fn($record) => $record->name)
                    ->recordSelectSearchColumns(['group_letter', 'group_level'])
                    ->recordSelectOptionsQuery(function ($query, AcademicYearService $academicYearService) {
                        $query->whereJsonContains('academic_year_ids', $academicYearService->getDefault()->external_id)
                            ->orderBy('group_level', 'asc')
                            ->orderBy('group_letter', 'asc');
                    })
                    ->preloadRecordSelect()])
            ->actions([Tables\Actions\DetachAction::make()
                ->recordTitle(fn($record) => $record->name)
                ->iconButton()
                ->before(fn($record) => $this->disassociateBookChapters(collect([$record]))),])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DetachBulkAction::make()
                ->before(fn($records) => $this->disassociateBookChapters($records))]),]);
    }

    private function updateBookGroup($column, $value, $record)
    {
        $bookId = $record->book_id;
        $groupId = $record->id;
        $groupLevel = $record->group_level;

        DB::table('book_group')
            ->where('book_id', $bookId)
            ->where('group_id', $groupId)
            ->limit(1)
            ->update([$column => $value]);

        $dateColumns = ['start_date', 'end_date','score'];
        if (in_array($column, $dateColumns)) {
            DB::table('book_group')
                ->join('groups', 'book_group.group_id', '=', 'groups.id')
                ->where('groups.group_level', $groupLevel)
                ->where('book_group.book_id', $bookId)
                ->whereNull($column)
                ->update([$column => $value]);
        }
    }

    private function disassociateBookChapters($records)
    {
        $chapters = $this->getOwnerRecord()->book_chapters;
        $group_ids = $records->pluck('pivot_group_id')->toArray();
        $groups = $this->getOwnerRecord()->groups->filter(fn($group) => in_array($group->id, $group_ids));
        app(BookChapterService::class)->disassociateGroups($chapters, $groups);
    }
}
