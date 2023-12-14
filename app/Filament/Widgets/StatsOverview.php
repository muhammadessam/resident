<?php

namespace App\Filament\Widgets;

use App\Models\Resident;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            Stat::make(' الذكور', Resident::male()->count())->description('عدد المقيمين الذكور')->color('success'),
            Stat::make(' الاناث', Resident::female()->count())->description('عدد المقيمين الاناث')->color('info'),
            Stat::make('الزيارات الخارجية', Visit::external()->count())->description('عدد الزيارات الخارحية الكلية')->color('danger'),
            Stat::make('الزيارات الداخلية', Visit::internal()->count())->description('عدد الزيارات الداخلية الكلية')->color('warning'),
        ];
    }
}
