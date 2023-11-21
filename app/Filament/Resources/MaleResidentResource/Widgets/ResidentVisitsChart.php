<?php

namespace App\Filament\Resources\MaleResidentResource\Widgets;

use App\Models\Resident;
use App\Models\Visit;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ResidentVisitsChart extends ChartWidget
{
    protected static ?string $heading = 'الزيارات الداخلية والخارجية للمقييم';
    public ?Resident $record = null;
    protected static ?string $maxHeight = '300px';
    public ?string $filter = '';

    protected function getFilterInfo(): array
    {
        return [
            '' => [
                'start' => Carbon::parse($this->record->visits()->min('date_time'))->subDay(),
                'end' => Carbon::parse($this->record->visits()->max('date_time'))->addYear(),
                'per' => 'year',
            ],
            'week' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
                'per' => 'day',
            ],
            'month' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
                'per' => 'day',
            ],
            'three_months' => [
                'start' => now()->startOfMonth()->sub('month', 3),
                'end' => now()->endOfMonth(),
                'per' => 'month',
            ],
            'year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
                'per' => 'month',
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 2;
    }


    protected function getFilters(): ?array
    {
        return [
            '' => 'الكل',
            'week' => 'اسبوع',
            'month' => 'شهر',
            'three_months' => '3 شهور',
            'year' => 'سنة',
        ];
    }

    protected function getData(): array
    {
        $internal = Trend::query($this->record->visits()->where('type', 'internal')->getQuery())
            ->dateColumn('date_time')
            ->between(
                start: $this->getFilterInfo()[$this->filter]['start'],
                end: $this->getFilterInfo()[$this->filter]['end'],
            )
            ->interval($this->getFilterInfo()[$this->filter]['per'])
            ->count();

        $external = Trend::query($this->record->visits()->where('type', 'external')->getQuery())
            ->dateColumn('date_time')
            ->between(
                start: $this->getFilterInfo()[$this->filter]['start'],
                end: $this->getFilterInfo()[$this->filter]['end'],
            )
            ->interval($this->getFilterInfo()[$this->filter]['per'])
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
