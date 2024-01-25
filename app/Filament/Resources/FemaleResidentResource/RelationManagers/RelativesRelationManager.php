<?php

namespace App\Filament\Resources\FemaleResidentResource\RelationManagers;

use App\Models\Relative;
use App\Models\RelativeResident;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Livewire;

class RelativesRelationManager extends RelationManager
{
    protected static string $relationship = 'relatives';
    protected static ?string $inverseRelationship = 'residents';
    protected static ?string $title = 'اقارب المقييم';
    protected static ?string $label = 'قريب';
    protected static ?string $pluralLabel = 'الاقارب';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
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
            TextInput::make('other_relation')
                ->label('صلة القرابة')
                ->hidden(fn(Get $get) => $get('relation') !== 'other')
                ->live(),
            Checkbox::make('is_guardian')->label('هل هذا القريب هو الوالي؟')
        ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label('الاسم'),
                TextColumn::make('id_number')->searchable()->label('رقم الهوية'),
                TextColumn::make('phone1')->searchable()->label('الهاتف 1'),
                TextColumn::make('phone2')->searchable()->label('الهاتف 2'),
                TextColumn::make('phone3')->searchable()->label('الهاتف 3'),
                TextColumn::make('relation')->label('صلة القرابة')
                    ->formatStateUsing(fn(string $state) => array_key_exists($state, RelativeResident::RELATION) ? RelativeResident::RELATION[$state] : $state),
                Tables\Columns\IconColumn::make('is_guardian')->boolean()->label('الوالي'),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()->hidden(function () {
                    return !$this->getOwnerRecord()->deleted_at;
                })->default(0),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function ($data) {
                    if ($data['relation'] == 'other') {
                        $data['relation'] = $data['other_relation'];
                    }
                    return $data;
                }),
                Tables\Actions\AttachAction::make()->form(fn(Tables\Actions\AttachAction $action) => [
                    $action->getRecordSelect()->preload()->live(),

                    Placeholder::make('id_number')
                        ->label('رقم الهوية')
                        ->content(fn(Get $get) => Relative::find($get('recordId'))->id_number)
                        ->hidden(fn($get) => $get('recordId') == null),

                    Select::make('relation')->label('صلة القرابة')
                        ->options(RelativeResident::RELATION)
                        ->live(),
                    TextInput::make('other_relation')->label('صلةالقرابة')->hidden(fn(Get $get) => $get('relation') !== 'other')->live(),
                    Checkbox::make('is_guardian')->label('هل هذا القريب هو الوالي؟')
                ])->mutateFormDataUsing(function ($data) {
                    if ($data['relation'] == 'other') {
                        $data['relation'] = $data['other_relation'];
                    }
                    return $data;
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->mutateFormDataUsing(function ($data) {
                    if ($data['relation'] == 'other') {
                        $data['relation'] = $data['other_relation'];
                    }
                    return $data;
                }),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\RestoreAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
