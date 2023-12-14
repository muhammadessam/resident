<?php

namespace App\Filament\Widgets;

use App\Models\Resident;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class ResidentCountWidget extends ChartWidget
{
    protected static ?string $heading = 'عدد المقيمين والمقيمات';
    protected static string $color = 'info';
    protected static ?string $pollingInterval = '10s';
    protected static ?string $maxHeight = '300px';

    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [Resident::male()->count(), Resident::female()->count()],
                    'backgroundColor' => '#205ba6',
                ],
            ],
            'labels' => ['عدد الذكور', 'عدد الاناث'],

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
        return 'doughnut';
    }
}
