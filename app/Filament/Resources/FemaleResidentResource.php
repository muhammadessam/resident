<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FemaleResidentResource\Pages;
use App\Models\Resident;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FemaleResidentResource extends Resource
{
    protected static ?string $model = Resident::class;

    protected static ?string $slug = 'female-residents';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'مقيم انثي';
    protected static ?string $pluralLabel = 'المقيمين الاناث';

    protected static ?string $navigationGroup = 'المقيمين';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-user-minus';

    public static function getEloquentQuery(): Builder
    {
        return Resident::query()->female();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('الاسم')->required(),

            TextInput::make('number')->label('رقم المستفيد')->required()->unique('residents', 'number'),

            DatePicker::make('dob')->label('تاريخ الميلاد')->required(),

            DatePicker::make('doe')->label('تاريخ الدخول')->required(),

            TextInput::make('building')->label('المبني')->required(),

            Select::make('mental_disability_degree')
                ->label('درجة الاعاقة')
                ->options(Resident::METALDEGREE)
                ->required(),


            Textarea::make('external_visit_authorized')->label('المصرح لهم بالزياة الخارجية')->required(),

            Textarea::make('internal_visit_authorized')->label('المصرح لهم بالزيارة الداخلية')->required(),

            Textarea::make('notes')->label('ملاحظات')->columnSpan(2)->required(),

            Checkbox::make('ability_to_external_visit')->label('القدرية علي الزيارة الخارجية'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('number'),

            TextColumn::make('dob')
                ->date(),

            TextColumn::make('doe')
                ->date(),

            TextColumn::make('building'),

            TextColumn::make('ability_to_external_visit'),

            TextColumn::make('external_visit_authorized'),

            TextColumn::make('internal_visit_authorized'),

            TextColumn::make('notes'),

            TextColumn::make('mental_disability_degree'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFemaleResidents::route('/'),
            'create' => Pages\CreateFemaleResident::route('/create'),
            'edit' => Pages\EditFemaleResident::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
