<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FemaleResidentResource\Pages;
use App\Filament\Resources\MaleResidentResource\RelationManagers\RelativesRelationManager;
use App\Filament\Resources\MaleResidentResource\RelationManagers\ResidentialRelativesRelationManager;
use App\Filament\Resources\MaleResidentResource\RelationManagers\VisitsRelationManager;
use App\Filament\Resources\MaleResidentResource\Widgets\ResidentVisitsChart;
use App\Models\Resident;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FemaleResidentResource extends Resource
{
    protected static ?string $model = Resident::class;

    protected static ?string $slug = 'female-residents';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'مقيم انثي';
    protected static ?string $pluralLabel = 'المقيمين الاناث';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-user-minus';
    protected static ?string $navigationLabel = 'قسم الاناث';

    public static function getEloquentQuery(): Builder
    {
        return Resident::query()->female()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            TextInput::make('name')->label('الاسم')->required(),

            TextInput::make('number')->label('رقم المستفيد')->required()->unique('residents', 'number', ignoreRecord: true),

            DatePicker::make('dob')->label('تاريخ الميلاد')->required(),

            DatePicker::make('doe')->label('تاريخ الدخول')->required(),

            Select::make('building')->label('المبني')->options(Resident::FEMALE_BUILDINGS)->required(),

            Select::make('city_id')->label('المدينة')->relationship('city', 'name')->preload()->searchable()->required(),

            Select::make('mental_disability_degree')->label('درجة الاعاقة')->options(Resident::METAL_DEGREE)->required(),

            Textarea::make('external_visit_authorized')->label('المصرح لهم بالزياة الخارجية'),

            Textarea::make('internal_visit_authorized')->label('المصرح لهم بالزيارة الداخلية'),

            Textarea::make('notes')->label('ملاحظات'),

            Select::make('healthProblems')->label('المشاكل الصحية')->multiple()->preload(true)->relationship('healthProblems', 'name'),

            Checkbox::make('ability_to_external_visit')->label('القدرية علي الزيارة الخارجية'),

            SpatieMediaLibraryFileUpload::make('visit_allow_report')
                ->collection('visit_allow_report')
                ->label('استمارة تصريح الزيارة'),

            SpatieMediaLibraryFileUpload::make('uploads')
                ->collection('uploads')
                ->multiple()
                ->label('مرفقات اخري'),

        ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('#')->sortable(),

            TextColumn::make('number')->label('رقم المستفيد')->sortable()->searchable(),

            TextColumn::make('name')->label('الاسم')->searchable()->sortable(),

            TextColumn::make('age')->label('العمر')->sortable(['dob'])->formatStateUsing(fn($state) => $state . ' سنة '),

            TextColumn::make('building')->label('المبني'),

            TextColumn::make('internal_visits_count')->label('عدد الزيارات الداخلية')->counts('internalVisits'),

            TextColumn::make('external_visits_count')->label('عدد الزيارات الخارجية')->counts('externalVisits'),

            TextColumn::make('lastVisit.date_time')->label('تاريخ اخر زيارة')->date('Y-m-d'),

        ])->actions([
            Action::make('move')
                ->action(action: fn(Resident $record) => $record->update(['type' => 'male']))
                ->icon('heroicon-o-user-minus')->requiresConfirmation(true)
                ->label('نقل'),
            ViewAction::make(),
            EditAction::make(),
            RestoreAction::make(),
            DeleteAction::make()->form([
                TextInput::make('deletion_reason')->required()->label('سبب الحذف'),
                DateTimePicker::make('deleted_at')->required()->label('تاريخ الحذف')->default(now()),
            ])->action(function (array $data, Resident $record) {
                $record->update($data);
                $record->delete();
            }),
        ])->filters([
            TrashedFilter::make(),

            TernaryFilter::make('ability_to_external_visit')->label('القدرة علي الزيارة الخارجية'),

            SelectFilter::make('mental_disability_degree')
                ->label('مستوي الاعاقة')->options(Resident::METAL_DEGREE)->preload(),

            SelectFilter::make('healthProblems')
                ->label('المشاكل الصحية')
                ->searchable()
                ->preload()
                ->multiple()
                ->relationship('healthProblems', 'name'),

            SelectFilter::make('city')->relationship('city', 'name')->multiple(),

            SelectFilter::make('building')->label('المبني')->options(Resident::MALE_BUILDINGS),

        ])->filtersFormColumns(2)->striped();
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
            VisitsRelationManager::class,
            RelativesRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'number'];
    }

    public static function getWidgets(): array
    {
        return [
            ResidentVisitsChart::class,
        ];
    }
}
