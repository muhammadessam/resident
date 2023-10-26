<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaleResidentResource\Pages;
use App\Models\Resident;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaleResidentResource extends Resource
{
    protected static ?string $model = Resident::class;

    protected static ?string $slug = 'male-residents';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'مقيم ذكر';

    protected static ?string $pluralLabel = 'المقيمين الذكور';
    protected static ?string $navigationGroup = 'المقيمين';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function getEloquentQuery(): Builder
    {
        return Resident::query()->male();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required(),

            DatePicker::make('dob'),

            DatePicker::make('doe'),

            TextInput::make('building')
                ->required(),

            Checkbox::make('ability_to_extrn_visit'),

            TextInput::make('extrn_visit_authorized')
                ->required(),

            TextInput::make('intrn_visit_authorized')
                ->required(),

            TextInput::make('notes')
                ->required(),

            TextInput::make('mental_disability_degree')
                ->required()
                ->integer(),

            Placeholder::make('created_at')
                ->label('Created Date')
                ->content(fn(?Resident $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Placeholder::make('updated_at')
                ->label('Last Modified Date')
                ->content(fn(?Resident $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('dob')
                ->date(),

            TextColumn::make('doe')
                ->date(),

            TextColumn::make('building'),

            TextColumn::make('ability_to_extrn_visit'),

            TextColumn::make('extrn_visit_authorized'),

            TextColumn::make('intrn_visit_authorized'),

            TextColumn::make('notes'),

            TextColumn::make('mental_disability_degree'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResidents::route('/'),
            'create' => Pages\CreateResident::route('/create'),
            'edit' => Pages\EditResident::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
