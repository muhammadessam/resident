<?php

namespace App\Filament\Resources\FemaleResidentResource\RelationManagers;

use App\Models\Resident;
use App\Models\ResidentRelative;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ResidentRelativesFemaleRelationManager extends RelationManager
{
    protected static string $relationship = 'residentRelatives';
    protected static ?string $label = 'مقيم قريب';
    protected static ?string $pluralLabel = 'المقيمين الاقارب';
    protected bool $allowsDuplicates = false;
    protected static ?string $title = 'المقيمين الاقارب';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(true)
                    ->recordSelectOptionsQuery(function (Resident $builder) {
                        return $builder->female()
                            ->where('id', '!=', $this->getOwnerRecord()->id)
                            ->whereNotIn('id', $this->getOwnerRecord()->residentRelatives->pluck('id'));
                    })->afterFormValidated(function ($data) {
                        ResidentRelative::firstOrCreate([
                            'resident_id' => $data['recordId'],
                            'relative_id' => $this->getOwnerRecord()->id,
                        ]);
                    })
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])->inverseRelationship('residentRelatives');
    }
}
