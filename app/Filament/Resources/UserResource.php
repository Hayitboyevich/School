<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Enums\GenderStatus;
use App\Models\Enums\UserStatus;
use App\Models\Group;
use App\Models\User;
use App\Services\AcademicYearService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\SelectColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'users/users';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('models/user.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/user.plural');
    }

    public static function form(Form $form): Form
    {
        $user = new User();

        return $form
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('models/user.prop.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label(__('models/user.prop.phone'))
                    ->required()
                    ->formatStateUsing(function ($record) {
                        return $record->formatted_phone;
                    })
                    ->mask('+999 99 999-99-99')
                    ->default('998')
                    ->placeholder('+___ __ ___-__-__')
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('email')
                    ->label(__('models/user.prop.email'))
                    ->unique(ignoreRecord: true)
                    ->email()
                    ->maxLength(255),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('password')
                        ->label(__('models/user.prop.password'))
                        ->password()
                        ->revealOnHover()
                        ->maxLength(255)
                        ->required(fn(string $context): bool => $context == 'create')
                        ->confirmed()
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->dehydrated(fn($state) => filled($state)),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label(__('models/user.prop.confirm_password'))
                        ->password()
                        ->revealOnHover()
                        ->maxLength(255),
                ]),
                Forms\Components\Select::make('roles')
                    ->label(__('models/user.relation.roles'))
                    ->multiple()
                    ->relationship('roles', 'label')
                    ->preload()
                    ->columnSpanFull(),

                Forms\Components\Section::make(__('models/user.prop.profile'))
                    ->description(__('models/user.prop.profile_info_fields'))
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label(__('models/user.prop.first_name'))
                            ->maxLength(50),
                        Forms\Components\TextInput::make('last_name')
                            ->label(__('models/user.prop.last_name'))
                            ->maxLength(50),
                        Forms\Components\TextInput::make('middle_name')
                            ->label(__('models/user.prop.middle_name'))
                            ->maxLength(50),
                        Forms\Components\Select::make('gender')
                            ->label(__('models/user.prop.gender'))
                            ->options(GenderStatus::class)
                            ->selectablePlaceholder(false),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label(__('models/user.prop.birth_date'))
                            ->before(now()->format('d.m.Y')),
                        Forms\Components\Select::make('status')
                            ->label(__('models/user.prop.status'))
                            ->required()
                            ->options(UserStatus::class)
                            ->default(UserStatus::ACTIVE)
                            ->selectablePlaceholder(false),
                    ])
                    ->columns(),
                Forms\Components\FileUpload::make('profile_photo_path')
                    ->image()
                    ->avatar()
                    ->disk($user->profilePhotoDisk())
                    ->directory($user->profilePhotoDirectory()),
                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('details.city')
                            ->label(__('models/city.singular'))
                            ->disabled(),
                        Forms\Components\TextInput::make('details.school')
                            ->label(__('models/school.singular'))
                            ->disabled(),
                        Forms\Components\TextInput::make('details.group')
                            ->label(mb_convert_case(__('models/group.singular'), MB_CASE_TITLE))
                            ->formatStateUsing(fn($record) => $record->groups()->first()->group_name ?? '')
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(UserResource::getEloquentQuery()->select('users.*'))
            ->columns([
                Tables\Columns\TextColumn::make('short_name')
                    ->label(__('models/user.prop.short_name'))
                    ->sortable(['first_name', 'last_name', 'name']),
                Tables\Columns\TextColumn::make('school')
                    ->label(__('models/school.singular')),
                Tables\Columns\TextColumn::make('groups')
                    ->label(__('models/user.prop.group'))
                    ->getStateUsing(fn(Model $record) => $record->groups->pluck('name'))
                    ->sortable(['name']),
                SelectColumn::make('status')
                    ->options(UserStatus::class)
                    ->selectablePlaceholder(false)
                    ->label(__('models/user.prop.status'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('models/city.singular'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('models/user.prop.number'))
                    ->copyable()
                    ->formatStateUsing(fn($state) => format_phone($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label(__('models/user.prop.gender'))
                    ->formatStateUsing(fn($state) => format_gender($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label(__('models/user.prop.birth_date'))
                    ->formatStateUsing(fn($state) => human_date($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('models/user.prop.data_register'))
                    ->formatStateUsing(fn($state) => human_date($state))
                    ->sortable(),
            ])
            ->paginated([50, 100, 200, 500, 'all'])
            ->filters([
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')->label(__('models/user.prop.short_name'))
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['name']) return null;
                        return $data['name'];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where(function ($query) use ($name) {
                                $query->where('users.name', 'like', '%' . $name . '%')
                                    ->orWhere('users.first_name', 'like', '%' . $name . '%')
                                    ->orWhere('users.last_name', 'like', '%' . $name . '%')
                                    ->orWhere('users.middle_name', 'like', '%' . $name . '%');
                            })
                        );
                    }),
                Tables\Filters\SelectFilter::make('groups')
                    ->label(__('models/user.prop.group'))
                    ->relationship('groups', 'group_name')
                    ->options(fn(AcademicYearService $academicYearService, Group $groups) => $groups
                        ->whereJsonContains('academic_year_ids', $academicYearService->getDefault()->external_id)
                    )
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(UserStatus::class)
                    ->multiple(),
                Tables\Filters\Filter::make('phone')
                    ->form([
                        Forms\Components\TextInput::make('phone')
                            ->label(__('models/user.prop.number'))
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['phone']) return null;
                        return $data['phone'];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['phone'],
                            function (Builder $query, $phone) {
                                return $query->where('phone', 'like', '%' . $phone . '%');
                            }
                        );
                    }),
                Tables\Filters\SelectFilter::make('gender')
                    ->label(__('models/user.prop.gender'))
                    ->options(GenderStatus::class)
                    ->multiple()
                    ->preload(),
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
