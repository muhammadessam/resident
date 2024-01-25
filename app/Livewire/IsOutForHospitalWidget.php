<?php

namespace App\Livewire;

use App\Models\Resident;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class IsOutForHospitalWidget extends BaseWidget
{
    public string $type = 'male';
    protected static ?string $heading = 'المقيميين الموجودين في المستشفي';

    public function table(Table $table): Table
    {
        return $table
            ->query(Resident::query()->{$this->type}()->where('is_out_to_hospital', '!=', null))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم المقييم'),
                Tables\Columns\TextColumn::make('building')->label('المبني')->formatStateUsing(fn($state) => Resident::BUILDINGS[$state]),
                Tables\Columns\TextColumn::make('is_out_to_hospital')->label('المبني')->date(),
            ]);
    }
}
