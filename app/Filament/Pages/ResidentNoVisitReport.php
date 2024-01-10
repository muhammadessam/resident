<?php

namespace App\Filament\Pages;

use App\Models\RelativeResident;
use App\Models\Resident;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ResidentNoVisitReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.resident-no-visit-report';
    protected static ?string $title = 'المقيمين الذين لم تتم زيارتهم';
    protected static ?string $navigationLabel = 'تقرير المقيمين الذبن لم تم زيارتهم';
    protected static ?string $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(RelativeResident::query())
            ->columns([
                TextColumn::make('relative.name')->label('اسم القريب'),
                TextColumn::make('relative.phone1')->label('رقم الجوال 1'),
                TextColumn::make('relative.phone2')->label('رقم الجوال 2'),
                TextColumn::make('relative.phone3')->label('رقم الجوال 3'),
            ])->defaultGroup(Group::make('resident.name')->label('اسم المقييم'));
    }

}
