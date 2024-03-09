<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Models\Relative;
use App\Models\RelativeResident;
use App\Models\Resident;
use App\Models\Visit;
use ArPHP\I18N\Arabic;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;
    protected static ?string $slug = 'visits';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $label = 'الزيارة';
    protected static ?string $pluralLabel = 'الزيارات';
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?int $navigationSort = 3;
    protected static ?bool $isDisabled = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('resident_id')
                ->label('المقييم')
                ->required()
                ->relationship('resident', 'name')
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, $set) {
                    $check_for_last_visit = Visit::where('resident_id', $state)
                        ->where('type', 'external')
                        ->whereDate('end_date', '>=', now());
                    $get_resident = Resident::find($state);
                    if ($check_for_last_visit?->count()) {
                        Notification::make()->danger()->title('المقيم في زيارة خارجية ')->body('عفواً هذا المقيم حالياً في زيارة خارجية')->persistent()->send();
                        self::$isDisabled = true;
                    }
                    if ($get_resident->is_out_to_hospital) {
                        Notification::make()->danger()->title('المقيم في المستشفي ')->body('عفواً هذا المقيم حالياً في المستشفي')->persistent()->send();
                        self::$isDisabled = true;
                    }
                    $set('relative_id', null);
                })->preload(),

            Select::make('relative_id')
                ->label('القريب')
                ->required()
                ->relationship('relative', 'name', function (Resident $builder, Get $get) {
                    return $builder->where('id', $get('resident_id'))->first()->relatives();
                })->live()
                ->hidden(fn(Get $get) => !($get('resident_id') !== null))
                ->preload()
                ->disabled(fn() => self::$isDisabled)
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')->required()->label('الاسم'),
                    TextInput::make('id_number')->required()->unique('relatives', 'id_number', ignoreRecord: true)->label('رقم الهوية'),
                    TextInput::make('phone1')->required()->label('الهاتف 1'),
                    TextInput::make('phone2')->label('الهاتف 2'),
                    TextInput::make('phone3')->label('الهاتف 3'),
                    Select::make('relation')
                        ->label('صلة القرابة')
                        ->options(RelativeResident::RELATION)
                        ->afterStateHydrated(function (Set $set, $state) {
                            if (!array_key_exists($state, RelativeResident::RELATION)) {
                                $set('other_relation', $state);
                                $set('relation', 'other');
                            }
                        })->live(),
                    TextInput::make('other_relation')->label('صلةالقرابة')->hidden(fn(Get $get) => $get('relation') !== 'other')->live(),
                    Checkbox::make('is_guardian')->label('هل هذا القريب هو الوالي؟')
                ])->createOptionUsing(function ($data, $model, $livewire, $get) {
                    if ($data['relation'] == 'other') {
                        $data['relation'] = $data['other_relation'];
                    }
                    Relative::create($data)->residents()->syncWithPivotValues($get('resident_id'), $data);
                })->fillEditOptionActionFormUsing(function (Get $get) {
                    $data = Relative::find($get('relative_id'))->only(['name', 'id_number', 'phone1', 'phone2', 'phone3']);
                    $data = array_merge($data, RelativeResident::where('resident_id', $get('resident_id'))->where('relative_id', $get('relative_id'))
                        ->first()->only(['relation', 'is_guardian']));
                    $data['other_relation'] = $data['relation'];
                    return $data;
                })->editOptionForm([
                    TextInput::make('name')->required()->label('الاسم'),
                    TextInput::make('id_number')->required()->unique('relatives', 'id_number', ignoreRecord: true)->label('رقم الهوية'),
                    TextInput::make('phone1')->required()->label('الهاتف 1'),
                    TextInput::make('phone2')->label('الهاتف 2'),
                    TextInput::make('phone3')->label('الهاتف 3'),
                    Select::make('relation')
                        ->label('صلة القرابة')
                        ->options(RelativeResident::RELATION)
                        ->afterStateHydrated(function (Set $set, $state) {
                            if (!array_key_exists($state, RelativeResident::RELATION)) {
                                $set('other_relation', $state);
                                $set('relation', 'other');
                            }
                        })->live(),
                    TextInput::make('other_relation')->label('صلةالقرابة')->hidden(fn(Get $get) => $get('relation') !== 'other')->live(),
                    Checkbox::make('is_guardian')->label('هل هذا القريب هو الوالي؟')
                ])->updateOptionUsing(function ($data, $get) {
                    Relative::find($get('relative_id'))->update($data);
                    if ($data['relation'] == 'other') {
                        $data['relation'] = $data['other_relation'];
                    }
                    RelativeResident::where('resident_id', $get('resident_id'))->where('relative_id', $get('relative_id'))
                        ->first()->update($data);
                }),

            Select::make('type')
                ->label('نوع الزيارة')
                ->required()
                ->disabled(fn() => self::$isDisabled)
                ->options(Visit::TYPE)->live(),

            DateTimePicker::make('date_time')
                ->disabled(fn() => self::$isDisabled)
                ->label('تاريخ ووقت الزيارة')
                ->default(now()),

            TextInput::make('duration')
                ->label('المدة')
                ->disabled(fn() => self::$isDisabled)
                ->required()
                ->integer()
                ->step(1)
                ->default(1)
                ->maxValue(90)
                ->minValue(1)
                ->hidden(fn(Get $get) => $get('type') != 'external'),

            Select::make('duration_type')
                ->label('المدة بالايام ام الساعات')
                ->required()
                ->disabled(fn() => self::$isDisabled)
                ->options([
                    'days' => 'يوم',
                    'hours' => 'ساعة',
                ])->hidden(fn(Get $get) => $get('type') != 'external'),


            TextInput::make('companion_no')
                ->label('عدد المرافقين')
                ->numeric()
                ->disabled(fn() => self::$isDisabled)
                ->maxValue(9)
                ->minValue(1)
                ->default(1)
                ->required(),

            Fieldset::make('المعلومات الخاص بكل من المقيم والقريب')->schema([
                Section::make('معلومات المقييم')
                    ->hidden(fn(Get $get) => $get('resident_id') == null)
                    ->schema([
                        Placeholder::make('name')->label('الاسم:')->inlineLabel()->content(fn(Get $get) => Resident::find($get('resident_id'))->name),
                        Placeholder::make('building')->label('المبني:')->inlineLabel()->content(fn(Get $get) => Resident::find($get('resident_id'))->building),
                        Placeholder::make('external_visit_authorized')
                            ->label('المصرح لهم بالزيارة الخارجية:')
                            ->content(fn(Get $get) => Resident::find($get('resident_id'))->external_visit_authorized),
                        Placeholder::make('internal_visit_authorized')
                            ->label('المصرح لهم بالزيارة الداخلية:')
                            ->content(fn(Get $get) => Resident::find($get('resident_id'))->internal_visit_authorized),

                        Placeholder::make('notes')
                            ->label('ملاحظات')
                            ->content(fn(Get $get) => Resident::find($get('resident_id'))->notes)

                    ])->columns(2)
                    ->compact()
                    ->columnSpan(1)
                    ->collapsible(),

                Section::make('معلومات القريب')
                    ->hidden(fn(Get $get) => $get('relative_id') == null)
                    ->schema([
                        Placeholder::make('name')->label('الاسم:')->inlineLabel()->content(fn(Get $get) => Relative::find($get('relative_id'))->name),
                        Placeholder::make('id_number')->label('رقم الهوية:')->inlineLabel()->content(fn(Get $get) => Relative::find($get('relative_id'))->id_number),
                        Placeholder::make('phone1')->label('رقم الجوال')->content(fn(Get $get) => Relative::find($get('relative_id'))->phone1)
                    ])->columnSpan(1)
                    ->columns(1)
                    ->compact()
                    ->collapsible(),
            ])->hidden(fn(Get $get) => ($get('relative_id') == null) and ($get('resident_id') == null)),
        ])->columns(2);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table->columns([

            TextColumn::make('resident.number')->label('رقم المستفيد')
                ->formatStateUsing(fn($state, Arabic $arabic) => $arabic->arNormalizeText($state, 'Hindu'))->searchable(),

            TextColumn::make('resident.name')->label('المقيم')->searchable(),

            TextColumn::make('relative.name')->label('القريب')->searchable(),

            TextColumn::make('relative.id_number')->label('رقم الهوية')->searchable(),

            TextColumn::make('relative.phone1')->label('رقم الجوال')->searchable(),

            TextColumn::make('type')->label('نوع الزيارة')->formatStateUsing(fn(Visit $visit) => Visit::TYPE[$visit->type]),

            TextColumn::make('duration')->label('مدة الزيارة')->formatStateUsing(fn(Visit $visit) => $visit->duration . ' ' . Visit::DURATION_TYPE[$visit->duration_type]),

            TextColumn::make('date_time')->label('تاريخ الزيارة')->date('Y-m-d'),

            TextColumn::make('time')->state(fn(Visit $record) => $record->date_time)->time('h:i A')->label('وقت الزيارة'),

            TextColumn::make('id')->label('الرقم المرجعي')->formatStateUsing(function ($state, Arabic $arabic) {
                return $arabic->arNormalizeText(str_pad($state, '5', '0', STR_PAD_LEFT), 'Hindu');
            })->searchable(),

        ])->filters([
            SelectFilter::make('resident')
                ->label('القسم')
                ->options(Resident::TYPE)
                ->query(function (Builder $query, array $data) {
                    $query->when($data['value'] ?? false, function (Builder $query, $value) {
                        $query->whereHas('resident', function (Builder $builder) use ($value) {
                            $builder->where('type', $value);
                        });
                    });
                }),

            SelectFilter::make('type')
                ->options(Visit::TYPE)
                ->label('نوع الزيارة'),

            Filter::make('date_time')
                ->form([
                    DatePicker::make('start')->label('بداية من: ')->format('d-m-Y')->inlineLabel(true),
                    DatePicker::make('end')->label('نهاية الي: ')->format('d-m-Y')->inlineLabel(true),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['start'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date_time', '>=', $date),
                        )
                        ->when(
                            $data['end'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date_time', '<=', $date),
                        );
                })->indicateUsing(function (array $data): ?string {
                    $indicator = null;
                    if ($data['start']) {
                        $indicator = 'بداية من تاريخ: ' . $data['start'];
                    }
                    if ($data['end']) {
                        $indicator .= ' الي تاريخ: ' . $data['end'];
                    }
                    return $indicator;
                })
        ])->actions(ActionGroup::make([
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->url(fn(Visit $record) => route('generate-visit-form', $record))->openUrlInNewTab(),
            ViewAction::make(), EditAction::make(), DeleteAction::make(),
        ]))->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
            'view' => Pages\ViewVisit::route('/{record}')
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
