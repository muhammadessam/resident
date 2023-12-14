<?php

namespace App\Filament\Widgets;

use App\Models\Resident;
use App\Models\Visit;
use ArPHP\I18N\Arabic;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestFiveVisits extends BaseWidget
{
    protected int|string|array $columnSpan = 2;
    protected static ?int $sort = 4;
    public $tableRecordsPerPage = 5;
    protected int|string|null $defaultTableRecordsPerPageSelectOption = 5;
    public function table(Table $table): Table
    {
        return $table
            ->query(Visit::query()->latest('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('resident.name')->label('اسم المقييم'),
                Tables\Columns\TextColumn::make('resident.type')->label('القسم')->formatStateUsing(fn($state) => Resident::TYPE[$state]),
                Tables\Columns\TextColumn::make('resident.building')->label('المبني')->formatStateUsing(fn($state) => Resident::BUILDINGS[$state]),
                Tables\Columns\TextColumn::make('relative.name')->label('اسم الزائر'),
                Tables\Columns\TextColumn::make('date_time')->label('التاريخ والوقت')
                    ->formatStateUsing(function ($state, Arabic $arabic) {
                        $arabic->setDateMode(3);
                        return '<span dir="rtl" class="my-1">' . $arabic->arNormalizeText($arabic->date('j / F / Y', $state->timestamp), 'Hindu') . '</span><br><span dir="rtl" class="my-1">' . $arabic->arNormalizeText($arabic->date('h:i A', $state->timestamp), 'Hindu') . '</span>';
                    })->html(true),
                Tables\Columns\TextColumn::make('type')->label('نوع الزيارة')->formatStateUsing(fn($state) => Visit::TYPE[$state]),
                Tables\Columns\TextColumn::make('duration')->label('مدة الزيارة')->formatStateUsing(fn(Visit $visit) => $visit->duration . ' ' . Visit::DURATION_TYPE[$visit->duration_type]),
            ]);
    }
}
