<?php

namespace App\Filament\Pages;

use App\Models\Resident;
use App\Models\Visit;
use App\Tables\Columns\ExternalVisitsDurationSum;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class VisitsReport21 extends Page implements HasTable
{

    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.visits-report21';
    protected static ?string $navigationGroup = 'التقارير';
    protected static ?string $title = 'تقرير الزيارات نموذج 21';


    public function table(Table $table): Table
    {
        return $table->query(Resident::query())->columns([
            TextColumn::make('name')->label('اسم المقييم')->sortable(),
            TextColumn::make('internal_visits_count')->label('عدد الزيارات الداخلية')->counts('internalVisits'),
            TextColumn::make('external_visits_count')->label('عدد الزيارات الخارجية')->counts('externalVisits'),
            ExternalVisitsDurationSum::make('external_duration_counts')->label('مدة الزيارات الخارجية'),
        ]);
    }
}
