<?php

namespace App\Filament\Widgets;

use App\Models\Resident;
use App\Models\Visit;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class VisitsCountPieWidget extends ChartWidget
{
    protected static ?string $heading = 'عدد الزيارات الداخلية والخارجية';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [Visit::internal()->count(), Visit::external()->count()],
                    'backgroundColor' => ['#205bb6', '#205bf6'],
                ],
            ],
            'labels' => ['عدد الزيارات الداخلية', 'عدد الزيارات الخارجية'],
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'tooltip' => [
                'rtl' => true,
            ],
            'legend' => [
                'rtl' => true,
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'display' => false
                    ],
                    'grid' => [
                        'display' => false,
                    ]
                ],
                'y' => [
                    'ticks' => [
                        'display' => false
                    ],
                    'grid' => [
                        'display' => false,
                    ]
                ],
            ]
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
