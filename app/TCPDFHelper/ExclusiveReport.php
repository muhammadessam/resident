<?php

namespace App\TCPDFHelper;


use Carbon\Carbon;
use setasign\Fpdi\Tcpdf\Fpdi;

class ExclusiveReport extends Fpdi
{
    public array $table_headers = [];
    public array $columns_width = [];

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        // set some language dependent data:
        $lg = array();
        $lg['a_meta_charset'] = 'UTF-8';
        $lg['a_meta_dir'] = 'rtl';
        $lg['a_meta_language'] = 'fa';
        $lg['w_page'] = 'page';

        // set some language-dependent strings (optional)
        $this->setLanguageArray($lg);
        $this->SetFont('aealarabiya', '', 10);

    }

    public function setupTableHeaders(array $columns): array
    {
        foreach ($columns as $column) {
            $this->table_headers[] = $column->getLabel();
        }
        return $this->table_headers;

    }

    public function getTableHeaderCount(): int
    {
        return count($this->table_headers);
    }

    public function coloredTable($data, $col_width = null, $file_path = null): void
    {
        $this->AddPage();
        if ($col_width) {
            $this->columns_width = $col_width;
        } else {
            $this->columns_width = array_fill(0, count($this->table_headers), ($this->getPageWidth() / count($this->table_headers)) - 5);
        }

        // Header
        $num_headers = $this->getTableHeaderCount();
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($this->columns_width[$i], 7, $this->table_headers[$i], 1, 0, 'C', 0);
        }
        $this->Ln();
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        // Data
        $fill = 0;
        foreach ($data as $row) {
            $this->Cell($this->columns_width[0], 7, $row->name ?? '', 'LR', 0, 'R', $fill);
            $this->Cell($this->columns_width[1], 7, $row->internal_visits_count ?? '', 'LR', 0, 'C', $fill);
            $this->Cell($this->columns_width[2], 7, $row->external_visits_count ?? '', 'LR', 0, 'C', $fill);
            $this->Cell($this->columns_width[3], 7, $row->visits->last() ? $row->visits->last()?->date_time->format('Y-m-d') : '', 'LR', 0, 'C', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($this->columns_width), 0, '', 'T');
        $this->Output($file_path ?? public_path('new.pdf'), 'F');
    }
}
