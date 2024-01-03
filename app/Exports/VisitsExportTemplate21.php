<?php

namespace App\Exports;

use App\Models\Resident;
use App\Models\Visit;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitsExportTemplate21 implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
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
        $data = Visit::external()
            ->where('resident_id', $row->id)
            ->selectRaw("SUM(duration * CASE duration_type WHEN 'days' THEN duration*24 WHEN 'hours' THEN duration*1 ELSE 0 END) AS total_duration")
            ->first();
        return [
            $row->name,
            $row->internal_visits_count ?: '0',
            $row->external_visits_count ?: '0',
            $data['total_duration'] ? CarbonInterval::hours($data['total_duration'])->cascade()->forHumans([
                'parts' => 4,
                'join' => ' و '
            ]) : '0',
            $row->lastVisit->date_time ?? '',
        ];
    }

    public function headings(): array
    {
        return ['اسم المقييم', 'عدد الزيارات الداخلية', 'عدد الزيارات الخارجية', 'مدة الزيارات الخارجية', 'تاريخ اخر الزيارة'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->getStyle('A:X')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        ];
    }
}
