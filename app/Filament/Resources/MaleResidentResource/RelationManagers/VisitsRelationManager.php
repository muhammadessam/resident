<?php

namespace App\Filament\Resources\MaleResidentResource\RelationManagers;

use App\Models\Relative;
use App\Models\RelativeResident;
use App\Models\Resident;
use App\Models\Visit;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'الزيارات الخاصة بالمقييم';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('relative_id')
                    ->label('القريب')
                    ->required()
                    ->relationship('relative', 'name', function (Resident $builder, Get $get) {
                        return $this->getOwnerRecord()->relatives();
                    })->live()
                    ->preload()
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
                    })
                    ->fillEditOptionActionFormUsing(function (Get $get) {
                        $data = Relative::find($get('relative_id'))->only(['name', 'id_number', 'phone1', 'phone2', 'phone3']);
                        $data = array_merge($data, RelativeResident::where('resident_id', $this->getOwnerRecord()->id)
                            ->where('relative_id', $get('relative_id'))
                            ->first()->only(['relation', 'is_guardian']));
                        $data['other_relation'] = $data['relation'];
                        return $data;
                    })
                    ->editOptionForm([
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
                    ->options([
                        'external' => 'زيارة خارجية',
                        'internal' => 'زيارة داخلية',
                    ])->live(),

                DateTimePicker::make('date_time')
                    ->label('تاريخ ووقت الزيارة')
                    ->default(now()),

                TextInput::make('duration')
                    ->label('المدة')
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
                    ->options([
                        'days' => 'يوم',
                        'hours' => 'ساعة',
                    ])->hidden(fn(Get $get) => $get('type') != 'external'),


                TextInput::make('companion_no')
                    ->label('عدد المرافقين')
                    ->numeric()
                    ->maxValue(9)
                    ->minValue(1)
                    ->default(1)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')->label('الرقم المرجعي')->formatStateUsing(fn($record) => str_pad($record->id, 5, '0', STR_PAD_LEFT)),

                TextColumn::make('relative.name')->label('اسم الزائر'),

                TextColumn::make('relative_relation')
                    ->label('صلة القرابة')
                    ->getStateUsing(function ($record) {
                        $relation = RelativeResident::where('resident_id', $record->resident_id)->where('relative_id', $record->relative_id)->first()->relation;
                        return array_key_exists($relation, RelativeResident::RELATION) ? RelativeResident::RELATION[$relation] : $relation;
                    }),

                TextColumn::make('type')->label('نوع الزيارة')->formatStateUsing(fn(Visit $visit) => Visit::TYPE[$visit->type]),

                TextColumn::make('duration')->label('مدة الزيارة')->formatStateUsing(fn(Visit $visit) => $visit->duration . ' ' . Visit::DURATION_TYPE[$visit->duration_type]),

                TextColumn::make('date_time')->label('تاريخ الزيارة'),

                TextColumn::make('companion_no')->label('عدد المرافقين'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
