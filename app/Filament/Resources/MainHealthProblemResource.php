<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MainHealthProblemResource\Pages;
use App\Models\MainHealthProblem;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MainHealthProblemResource extends Resource
{
    protected static ?string $model = MainHealthProblem::class;

    protected static ?string $slug = 'main-health-problems';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $label = 'مشكلة صحية';
    protected static ?string $pluralLabel = 'المشاكل الصحية';

    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required(),
            Repeater::make('subHealthProblems')
                ->label('المشاكل الفرعية')
                ->relationship('subHealthProblems')
                ->schema([
                    TextInput::make('name')->label('الاسم')->required(),
                ])->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label('الاسم')
                ->searchable()
                ->sortable(),
            TextColumn::make('subHealthProblems.name')->label('المشاكل الفرعية')
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMainHealthProblems::route('/'),
            'create' => Pages\CreateMainHealthProblem::route('/create'),
            'edit' => Pages\EditMainHealthProblem::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
