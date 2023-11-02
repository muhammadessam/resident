<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FemaleResidentResource\Pages;
use App\Filament\Resources\FemaleResidentResource\RelationManagers\ResidentialRelativesRelationManager;
use App\Models\Resident;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
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

            TextInput::make('number')->label('رقم المستفيد')
                ->required()
                ->unique('residents', 'number', ignoreRecord: true),

            DatePicker::make('dob')->label('تاريخ الميلاد')->required(),

            DatePicker::make('doe')->label('تاريخ الدخول')->required(),

            TextInput::make('building')->label('المبني')->required(),

            Select::make('mental_disability_degree')
                ->label('درجة الاعاقة')
                ->options(Resident::METALDEGREE)
                ->required(),


            Textarea::make('external_visit_authorized')->label('المصرح لهم بالزياة الخارجية'),

            Textarea::make('internal_visit_authorized')->label('المصرح لهم بالزيارة الداخلية'),

            Textarea::make('notes')->label('ملاحظات'),

            Select::make('healthProblems')
                ->label('المشاكل الصحية')
                ->required()
                ->multiple()
                ->preload(true)
                ->relationship('healthProblems', 'name'),

            SpatieMediaLibraryFileUpload::make('visit_allow_report')
                ->collection('visit_allow_report')
                ->label('استمارة تصريح الزيارة'),

            SpatieMediaLibraryFileUpload::make('uploads')
                ->collection('uploads')
                ->multiple()
                ->label('مرفقات اخري'),


            Checkbox::make('ability_to_external_visit')->label('القدرية علي الزيارة الخارجية'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('#')->sortable(),
            TextColumn::make('number')->label('رقم المستفيد')->sortable(),
            TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
            TextColumn::make('age')->label('العمر'),
            TextColumn::make('building')->label('المبني'),
        ])->actions([
            ViewAction::make(), EditAction::make(), DeleteAction::make()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFemaleResidents::route('/'),
            'create' => Pages\CreateFemaleResident::route('/create'),
            'view' => Pages\ViewFemaleResident::route('/{record}'),
            'edit' => Pages\EditFemaleResident::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ResidentialRelativesRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
