<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class VisitsCountWidget extends ChartWidget
{
    protected static ?string $heading = 'مؤشر عدد الزيارات';

    protected static ?int $sort = 3;
    public ?string $filter = 'year';
    protected static ?string $pollingInterval = '30s';
    protected function getFilterInfo(): array
    {
        return [
            '' => [
                'start' => Carbon::parse(Visit::min('date_time'))->subDay(),
                'end' => Carbon::parse(Visit::max('date_time'))->addYear(),
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
        $internal_male = Trend::query(Visit::internal()->whereHas('resident', function (Builder $builder) {
            $builder->where('type', 'male');
        }))->dateColumn('date_time')
            ->between(
                start: $this->getFilterInfo()[$this->filter]['start'],
                end: $this->getFilterInfo()[$this->filter]['end'],
            )
            ->interval($this->getFilterInfo()[$this->filter]['per'])
            ->count();
        $external_male = Trend::query(Visit::external()->whereHas('resident', function (Builder $builder) {
            $builder->where('type', 'male');
        }))->dateColumn('date_time')
            ->between(
                start: $this->getFilterInfo()[$this->filter]['start'],
                end: $this->getFilterInfo()[$this->filter]['end'],
            )
            ->interval($this->getFilterInfo()[$this->filter]['per'])
            ->count();

        $internal_female = Trend::query(Visit::internal()->whereHas('resident', function (Builder $builder) {
            $builder->where('type', 'female');
        }))->dateColumn('date_time')
            ->between(
                start: $this->getFilterInfo()[$this->filter]['start'],
                end: $this->getFilterInfo()[$this->filter]['end'],
            )
            ->interval($this->getFilterInfo()[$this->filter]['per'])
            ->count();
        $external_female = Trend::query(Visit::internal()->whereHas('resident', function (Builder $builder) {
            $builder->where('type', 'female');
        }))->dateColumn('date_time')
            ->between(
                start: $this->getFilterInfo()[$this->filter]['start'],
                end: $this->getFilterInfo()[$this->filter]['end'],
            )
            ->interval($this->getFilterInfo()[$this->filter]['per'])
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الزيارات الداخلية للذكور',
                    'data' => $internal_male->map(fn(TrendValue $value) => $value->aggregate),
                    "borderColor" => '#2ba141',
                    "backgroundColor" => '#2ba141',

                ],
                [
                    'label' => 'عدد الزيارات الخارجية للذكور',
                    'data' => $external_male->map(fn(TrendValue $value) => $value->aggregate),
                    "borderColor" => '#a73e6d',
                    "backgroundColor" => '#a73e6d',
                ],
                [
                    'label' => 'عدد الزيارات الداخلية للاناث',
                    'data' => $internal_female->map(fn(TrendValue $value) => $value->aggregate),
                    "borderColor" => '#175bb3',
                    "backgroundColor" => '#175bb3',
                ],
                [
                    'label' => 'عدد الزيارات الخارجية للاناث',
                    'data' => $external_female->map(fn(TrendValue $value) => $value->aggregate),
                    "borderColor" => '#1a71a9',
                    "backgroundColor" => '#1a71a9',
                ],
            ],
            'labels' => $external_female->map(fn(TrendValue $value) => $value->date),

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
                'display' => true,
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
