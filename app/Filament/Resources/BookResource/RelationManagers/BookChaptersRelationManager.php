<?php

namespace App\Filament\Resources\BookResource\RelationManagers;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Group;
use App\Services\BookChapterService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class BookChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'book_chapters';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Разделы книги';

    public static function getModelLabel(): ?string
    {
        return __('models/book_chapter.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/book_chapter.plural');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('models/book_chapter.prop.name'))
                    ->required()
                    ->minLength(2)
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('page_start')
                    ->label(__('models/book_chapter.prop.page_start'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('page_end')
                    ->label(__('models/book_chapter.prop.page_end'))
                    ->maxLength(255)
            ]);
    }

    public function getTabs(): array
    {
        $chapters = $this->getOwnerRecord()->book_chapters;
        $groups = $this->getOwnerRecord()->groups->sortBy(['group_level', 'asc'], ['group_letter', 'asc']);
        app(BookChapterService::class)->associateGroups($chapters, $groups);
        $tabs = [];
        foreach ($groups as $group) {
            $tabs[] = Tab::make($group->name)
                ->modifyQueryUsing(fn($query) => $query
                    ->select('book_chapters.*', 'book_chapter_group.group_id', 'book_chapter_group.score', 'book_chapter_group.start_date', 'book_chapter_group.end_date')
                    ->leftJoin('book_chapter_group', 'book_chapter_group.book_chapter_id', 'book_chapters.id')
                    ->where('book_chapter_group.group_id', $group->id));
        }
        return $tabs;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')->label('№')->sortable(),
                Tables\Columns\TextColumn::make('name')->label(__('models/book_chapter.prop.name'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('page_start')->label(__('models/book_chapter.prop.page_start'))->sortable(),
                Tables\Columns\TextColumn::make('page_end')->label(__('models/book_chapter.prop.page_end'))->sortable(),
                Tables\Columns\TextInputColumn::make('score')
                    ->label(__('models/book_chapter.prop.score'))
                    ->type('number')
                    ->updateStateUsing(fn($state, $record) => $this->updateBookChapterGroup('score', $state, $record))
                    ->sortable(['book_chapter_group.score']),
                Tables\Columns\TextInputColumn::make('start_date')
                    ->label(__('models/group.prop.start_date'))
                    ->type('date')
                    ->updateStateUsing(fn($state, $record) => $this->updateBookChapterGroup('start_date', $state, $record))
                    ->sortable(['book_chapter_group.start_date']),
                Tables\Columns\TextInputColumn::make('end_date')
                    ->label(__('models/group.prop.end_date'))
                    ->type('date')
                    ->updateStateUsing(fn($state, $record) => $this->updateBookChapterGroup('end_date', $state, $record))
                    ->sortable(['book_chapter_group.end_date'])
            ])
            ->paginated(['25', '50', '100', 'all'])
            ->filters([
                Tables\Filters\Filter::make('order'),
                Tables\Filters\Filter::make('score')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('models/book.relation.add_section'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->reorderable('order')
            ->defaultSort('order');
    }

    private function updateBookChapterGroup($column, $value, $record)
    {

        $currentGroup = Group::find($record->group_id);
        $currentGroupId = $currentGroup->id;
        $currentGroupLevel = $currentGroup->group_level;

        DB::table('book_chapter_group')
            ->where('book_chapter_id', $record->id)
            ->where('group_id', $currentGroupId)
            ->limit(1)
            ->update([$column => $value]);

        $otherGroups = DB::table('book_chapter_group')
            ->join('groups', 'book_chapter_group.group_id', '=', 'groups.id')
            ->where('book_chapter_id', $record->id)
            ->where('groups.group_level', $currentGroupLevel)
            ->where('groups.id', '<>', $currentGroupId)
            ->get();

        foreach ($otherGroups as $otherGroup) {
            $existingValue = DB::table('book_chapter_group')
                ->where('book_chapter_id', $record->id)
                ->where('group_id', $otherGroup->id)
                ->value($column);

            if ($existingValue === null) {
                DB::table('book_chapter_group')
                    ->where('book_chapter_id', $record->id)
                    ->where('group_id', $otherGroup->id)
                    ->limit(1)
                    ->update([$column => $value]);
            }
        }
    }
}
