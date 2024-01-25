<?php

namespace App\Livewire;

use App\Models\Resident;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaleResidentInDeptWidget extends BaseWidget
{
    public $type = 'male';

    public function mout(string $type)
    {

    }

    protected function getStats(): array
    {
        return [
            Stat::make('عدد المقيمين', Resident::{$this->type}()->whereDoesntHave('activeExternalVisit')->where('is_out_to_hospital', null)->count())
                ->description('المقيميين حالياً داخل القسم')->color('success'),

            Stat::make('الزيارات الخارجية', Resident::{$this->type}()->whereHas('activeExternalVisit')->count())
                ->description('المقيميين الموجودين في زيارة خارجية الان')->color('info'),

            Stat::make('المستشفي', Resident::{$this->type}()->where('is_out_to_hospital', '!=', null)->count())
                ->description('المقيميين المتواجدين في المستشفي حاليا')->color('danger'),

        ];
    }
}
