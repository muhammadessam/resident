<?php

namespace App\Exports;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExclusiveReportExport implements FromCollection, WithMapping, WithHeadings
{

    public Collection $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return $this->data;
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->internal_visits_count,
            $row->external_visits_count,
            $row->visits()->latest()->first()->date_time,
        ];
    }
    public function headings(): array
    {
        return ['اسم المقييم', 'عدد الزيارات الداخلية', 'عدد الزيارات الخارجية', 'تاريخ الزيارة'];
    }
}
