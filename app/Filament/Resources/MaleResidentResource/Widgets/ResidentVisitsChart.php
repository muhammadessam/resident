<?php

namespace App\Filament\Resources\MaleResidentResource\Widgets;

use App\Models\Visit;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ResidentVisitsChart extends ChartWidget
{
    protected static ?string $heading = 'الزيارات الداخلية والخارجية للمقييم';

    protected static ?string $maxHeight = '300px';
    public function getColumnSpan(): int|string|array
    {
        return 2;
    }

    protected function getFilters(): ?array
    {
        return [
            '' => '',
            'week' => 'اسبوع',
            'month' => 'شهر',
            'three_months' => '3 شهور',
            'year' => 'سنة',
        ];
    }

    protected function getData(): array
    {
        $internal = Trend::query(Visit::where('type', 'internal'))
            ->dateColumn('date_time')
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->count();

        $external = Trend::query(Visit::where('type', 'external'))
            ->dateColumn('date_time')
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'الزيارات الداخلية',
                    'data' => $internal->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
                [
                    'label' => 'الزيارات الداخلية',
                    'data' => $external->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'black',
                    'borderColor' => 'black',
                ],

            ],
            'labels' => $internal->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
