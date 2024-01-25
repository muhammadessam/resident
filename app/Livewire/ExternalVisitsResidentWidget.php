<?php

namespace App\Livewire;

use App\Models\Resident;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class ExternalVisitsResidentWidget extends BaseWidget
{
    public string $type = 'male';

    protected static ?string $heading = 'الزيارات الخارجية';

    public function table(Table $table): Table
    {
        return $table
            ->query(Resident::query()->{$this->type}()->whereHas('activeExternalVisit'))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم المقييم'),
                Tables\Columns\TextColumn::make('building')->label('المبني')->formatStateUsing(fn($state) => Resident::BUILDINGS[$state]),
                Tables\Columns\TextColumn::make('activeExternalVisit.end_date')->label('ستنتهي الزيارة خلال')->date()->since(),
            ]);
    }
}
