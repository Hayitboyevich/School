<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers\BookChaptersRelationManager;
use App\Filament\Resources\BookResource\RelationManagers\GroupsRelationManager;
use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\BookAuthor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $slug = 'books/books';

    public static function getModelLabel(): string
    {
        return __('models/book.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/book.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('models/book.prop.name'))
                    ->required()
                    ->minLength(2)
                    ->columnSpanFull()
                    ->maxLength(1000),
                Forms\Components\RichEditor::make('description')
                    ->label(__('models/book.prop.description'))
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('cover')
                    ->label(__('models/book.prop.cover'))
                    ->image()
                    ->maxSize(2048)
                    ->disk('public')
                    ->directory('book_covers'),
                Forms\Components\FileUpload::make('file_url')
                    ->label(__('models/book.prop.file_url'))
                    ->maxSize(40960)
                    ->acceptedFileTypes([
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/pdf',
                        'text/plain',
                        'image/vnd.djvu',
                        'application/epub+zip',
                        'application/fb2+xml'
                    ])
                    ->disk('public')
                    ->directory('books'),
                Forms\Components\Textarea::make('reference_link')
                    ->label(__('models/book.prop.reference_link'))
                    ->columnSpanFull()
                    ->maxLength(2000),
                Forms\Components\Select::make('book_authors')
                    ->label(__('models/book.relation.book_author'))
                    ->multiple()
                    ->relationship('book_authors', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn(BookAuthor $record) => $record->full_name)
                    ->preload()
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('page_count')
                    ->label(__('models/book.prop.page_count'))
                    ->integer()
                    ->columnSpanFull(),
                Forms\Components\Select::make('genres')
                    ->label(__('models/book.relation.genres'))
                    ->multiple()
                    ->relationship('genres', 'name')
                    ->preload()
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Select::make('academic_years')
                    ->label(__('models/book.relation.academic_years'))
                    ->multiple()
                    ->relationship('academic_years', 'start')
                    ->getOptionLabelFromRecordUsing(fn(AcademicYear $record) => $record->period)
                    ->preload()
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('models/book.prop.name'))->wrap(),
                Tables\Columns\ImageColumn::make('cover')->label(__('models/book.prop.cover')),
                Tables\Columns\TextColumn::make('page_count')->label(__('models/book.prop.page_count')),
                Tables\Columns\TextColumn::make('book_chapters_count')->label(__('models/book.relation.book_chapters_count'))->counts('book_chapters'),
                Tables\Columns\TextColumn::make('book_authors.full_name')->label(__('models/book.relation.book_authors'))->wrap(),
                Tables\Columns\TextColumn::make('groups.group_name')->label(__('models/book.relation.groups'))->wrap(),
                Tables\Columns\TextColumn::make('deadlines')->label(__('models/book.prop.deadlines'))
                    ->state(function ($record) {
                        $allGroupsHaveDeadlines = $record->groups->every(fn($group) => $group->pivot->start_date && $group->pivot->end_date);
                        return $allGroupsHaveDeadlines ? __('models/book.prop.specified') : __('models/book.prop.not_specified');
                    }),
                Tables\Columns\TextColumn::make('genres.name')->label(__('models/book.relation.genres'))->wrap(),
            ])
            ->filters([
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')->label(__('models/book.prop.name'))
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['name']) return null;
                        return $data['name'];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['name'],
                                fn(Builder $query, $name): Builder => $query->where('name', 'like', '%' . $name . '%'),
                            );
                    }),
                Tables\Filters\SelectFilter::make('book_authors')
                    ->relationship('book_authors', 'full_name')
                    ->label(__('models/book.relation.book_authors'))
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name),
                Tables\Filters\SelectFilter::make('groups')
                    ->relationship('groups', 'group_name')
                    ->label(__('models/book.relation.groups'))
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->group_name),
                Tables\Filters\SelectFilter::make('genres')
                    ->relationship('genres', 'name')
                    ->label(__('models/book.relation.genres'))
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name),
                Tables\Filters\TernaryFilter::make('deadlines')
                    ->label(__('models/book.prop.deadlines'))
                    ->placeholder(__('models/book.prop.all'))
                    ->trueLabel(__('models/book.prop.specified'))
                    ->falseLabel(__('models/book.prop.not_specified'))
                    ->queries(
                        true: function (Builder $query) {
                            $query->whereDoesntHave('groups', function (Builder $query) {
                                $query->whereNull('start_date')->orWhereNull('end_date');
                            });
                        },
                        false: function (Builder $query) {
                            $query->whereHas('groups', function ($query) {
                                $query->whereNull('start_date')->orWhereNull('end_date');
                            });
                        },
                        blank: fn (Builder $query) => $query,
                    ),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)->filtersFormColumns(5)
            ->paginated(['25', '50', '100', '200', '300', 'all'])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Relations', [
                GroupsRelationManager::class,
                BookChaptersRelationManager::class
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
