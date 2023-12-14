<?php

namespace App\Filament\Widgets;

use App\Models\Resident;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('المقيمين الذكور', Resident::male()->count()),
            Stat::make('المقيمين الاناث', Resident::female()->count()),


        ];
    }
}
