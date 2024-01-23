<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $slug = 'schools/academic-years';

    public static function getModelLabel(): string
    {
        return __('models/academic_year.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models/academic_year.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('start')
                    ->label(__('models/academic_year.prop.start'))
                    ->closeOnDateSelection()
                    ->required(),
                Forms\Components\DatePicker::make('end')
                    ->label(__('models/academic_year.prop.end'))
                    ->closeOnDateSelection()
                    ->required()
                    ->after('start'),
                Forms\Components\DatePicker::make('quarter1_start_date')
                    ->label(__('models/academic_year.prop.quarter1_start'))
                    ->closeOnDateSelection()
                    ->afterOrEqual('start'),
                Forms\Components\DatePicker::make('quarter1_end_date')
                    ->label(__('models/academic_year.prop.quarter1_end'))
                    ->closeOnDateSelection()
                    ->after('quarter1_start_date'),
                Forms\Components\DatePicker::make('quarter2_start_date')
                    ->label(__('models/academic_year.prop.quarter2_start'))
                    ->closeOnDateSelection()
                    ->after('quarter1_end_date'),
                Forms\Components\DatePicker::make('quarter2_end_date')
                    ->label(__('models/academic_year.prop.quarter2_end'))
                    ->closeOnDateSelection()
                    ->after('quarter2_start_date'),
                Forms\Components\DatePicker::make('quarter3_start_date')
                    ->label(__('models/academic_year.prop.quarter3_start'))
                    ->closeOnDateSelection()
                    ->after('quarter2_end_date'),
                Forms\Components\DatePicker::make('quarter3_end_date')
                    ->label(__('models/academic_year.prop.quarter3_end'))
                    ->closeOnDateSelection()
                    ->after('quarter3_start_date'),
                Forms\Components\DatePicker::make('quarter4_start_date')
                    ->label(__('models/academic_year.prop.quarter4_start'))
                    ->closeOnDateSelection()
                    ->after('quarter3_end_date'),
                Forms\Components\DatePicker::make('quarter4_end_date')
                    ->label(__('models/academic_year.prop.quarter4_end'))
                    ->closeOnDateSelection()
                    ->after('quarter4_start_date')
                    ->beforeOrEqual('end')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period')->label(__('models/academic_year.prop.period'))->wrap(),
                Tables\Columns\TextColumn::make('fullPeriod')->label(__('models/academic_year.prop.full'))->wrap(),
                Tables\Columns\TextColumn::make('quarter1')->label(__('models/academic_year.prop.quarter1'))->getStateUsing(fn(AcademicYear $record) => $record->getQuarterPeriod(1))->wrap(),
                Tables\Columns\TextColumn::make('quarter2')->label(__('models/academic_year.prop.quarter2'))->getStateUsing(fn(AcademicYear $record) => $record->getQuarterPeriod(2))->wrap(),
                Tables\Columns\TextColumn::make('quarter3')->label(__('models/academic_year.prop.quarter3'))->getStateUsing(fn(AcademicYear $record) => $record->getQuarterPeriod(3))->wrap(),
                Tables\Columns\TextColumn::make('quarter4')->label(__('models/academic_year.prop.quarter4'))->getStateUsing(fn(AcademicYear $record) => $record->getQuarterPeriod(4))->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAcademicYears::route('/'),
        ];
    }
}
