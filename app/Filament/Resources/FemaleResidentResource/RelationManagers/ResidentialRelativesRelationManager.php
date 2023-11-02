<?php

namespace App\Filament\Resources\FemaleResidentResource\RelationManagers;

use App\Models\Resident;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;


class ResidentialRelativesRelationManager extends RelationManager
{
    protected static string $relationship = 'residentialRelatives';
    protected static ?string $inverseRelationship = 'relativesResidential';
    protected bool $allowsDuplicates = false;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (Resident $resident) {
                        return $resident->female()->whereNotIn('id', $this->getOwnerRecord()
                            ->residentialRelatives
                            ->pluck('id')
                            ->push($this->getOwnerRecord()->id)
                        );
                    })->after(function ($livewire, $data) {
                        \DB::table('resident_resident')->insert([
                            'resident_id' => $data['recordId'],
                            'relative_id' => $this->getOwnerRecord()->id
                        ]);
                    }),
            ])->actions([
                Tables\Actions\DetachAction::make()->after(function ($livewire) {
                    \DB::table('resident_resident')
                        ->where('relative_id', $this->getOwnerRecord()->id)
                        ->where('resident_id', $livewire->mountedTableActionRecord)->delete();

                })
            ]);
    }
}
