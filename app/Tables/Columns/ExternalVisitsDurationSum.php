<?php

namespace App\Tables\Columns;

use App\Models\Visit;
use Carbon\CarbonInterval;
use Filament\Tables\Columns\Column;

class ExternalVisitsDurationSum extends Column
{
    protected string $view = 'tables.columns.external-visits-duration-sum';

    public function getState(): string
    {
        $data = Visit::external()
            ->where('resident_id', $this->getRecord()->id)
            ->selectRaw("SUM(duration * CASE duration_type WHEN 'days' THEN duration*24 WHEN 'hours' THEN duration*1 ELSE 0 END) AS total_duration")
            ->first();
        return $data['total_duration'] ? CarbonInterval::hours($data['total_duration'])->cascade()->forHumans([
            'parts' => 4,
            'join' => ' و '
        ]) : '0';
    }
}
