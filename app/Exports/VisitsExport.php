<?php

namespace App\Exports;

use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitsExport implements WithEvents, WithHeadings, ShouldAutoSize, WithMapping, WithStyles, FromCollection
{

    private Collection $data;

    public function __construct(Collection $data = null)
    {
        $this->data = $data ?: Visit::query();
    }

    public function collection(): Collection
    {
        return $this->data;
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


    public function headings(): array
    {
        return [
            'اسم المقيم', 'المبني', 'اسم الزائر', 'رقم الهوية', 'الهاتف', 'عدد المرافقين', 'وقت الزيارة', 'وقت الخروج'
        ];
    }

    public function map($row): array
    {
        return [
            $row->resident->name,
            $row->resident->building,
            $row->relative->name,
            $row->relative->id_number,
            $row->relative->phone1,
            $row->companion_no,
            Carbon::parse($row->date_time),
            Carbon::parse($row->date_time)->add($row->duration_type, $row->duration),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
