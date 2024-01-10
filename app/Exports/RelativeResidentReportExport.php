<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RelativeResidentReportExport implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{

    public Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    public function map($row): array
    {
        return [
            $row->resident->name ?? '',
            $row->relative->name ?? '',
            $row->relative->phone1 ?? '',
            $row->relative->phone2 ?? '',
            $row->relative->phone3 ?? '',
        ];
    }

    public function headings(): array
    {
        return ['اسم المقييم', 'اسم القريب', 'رقم الجوال 1', 'رقم الجوال 2', 'رقم الجوال 3'];
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
