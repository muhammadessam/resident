<?php

namespace App\TCPDFHelper;


use App\Models\Relative;
use App\Models\RelativeResident;
use App\Models\Visit;
use ArPHP\I18N\Arabic;
use Carbon\Carbon;
use setasign\Fpdi\Tcpdf\Fpdi;

class VisitsForm extends Fpdi
{
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
        $font_name = \TCPDF_FONTS::addTTFfont(public_path('fonts/alfont_com_arial-1.ttf'), 'TrueTypeUnicode', 'UTF-8', 96);
        $this->setFont($font_name);
        $this->setPrintHeader(false);
        $this->SetPrintFooter(false);
    }

    public function coloredTable(Visit $visit): void
    {
        $cell_height = 7;
        // Page header and image and title.
        $ar = new Arabic();
        $ar->setDateMode(3);
        $this->AddPage();
        $this->setRTL(true);
        $this->Image(public_path('logo.png'), '', '', '40', '20', 'PNG', '', 'R', border: 0);
        $this->setXY($this->getPageWidthWithMargins() * 0.5, 10);
        $this->Cell($this->getPageWidthWithMargins() * 0.5, 20, 'الرقم المرجعي للزيارة: ' . $ar->arNormalizeText(str_pad($visit->id, '5', '0', STR_PAD_LEFT), 'Hindu'), 0, 1, 'L', 0);

        $this->Cell($this->getPageWidthWithMargins(), 7, 'نموذج رقم (19)', 0, '1', 'C');
        $this->Cell($this->getPageWidthWithMargins(), 10, '(استمارة الزيارات)', 0, '1', 'C');


        // Page first table.
        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'اليوم', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, $visit->date_time->dayOfWeek, 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'التاريخ', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, $ar->arNormalizeText($ar->date('Y/m/d', $visit->date_time->timestamp), 'Hindu'), 1, 1, 'C');


        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'اسم المستفيد', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, $visit->relative->name, 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'نوع الزيارة', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, Visit::TYPE[$visit->type], 1, 1, 'C');

        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'وقت الزيارة', 1, 0, 'C');
            $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, $ar->arNormalizeText($ar->date('h:i a', $visit->date_time->timestamp), 'Hindu'), 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'مدة الزيارة', 1, 0, 'C');
            $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, $ar->arNormalizeText($visit->duration . ' ' . Visit::DURATION_TYPE[$visit->duration_type], 'Hindu'), 1, 1, 'C');

        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'اسم مسجل البيانات', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, auth()->user()->name, 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'اخصائي المتابعة', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, '', 1, 1, 'C');

        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'الطبيب', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, '', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.2, $cell_height, 'الممرض', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.3, $cell_height, '', 1, 1, 'C');

        $this->Ln(10);
        $this->Cell($this->getPageWidthWithMargins() * 0.5, $cell_height, 'القائم بالزيارة من الاسرة المصرح لها', 1, 0, 'C');
//        $this->Cell($this->getPageWidthWithMargins() * 0.5, $cell_height, array_key_exists($visit->relative->residents()->where('residents.id', $visit->resident_id)->first()->pivot->relation, RelativeResident::RELATION) ? RelativeResident::RELATION[$visit->relative->residents()->where('residents.id', $visit->resident_id)->first()->pivot->relation] : $visit->relative->residents()->where('residents.id', $visit->resident_id)->first()->pivot->relation, 1, 1, 'C');
        $this->writeHTMLCell($this->getPageWidthWithMargins() * 0.5, $cell_height, '', '', $this->writeRelationChoices('father'), 1, 1, '', 0, 'J');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, 'اسم الزائر', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, $visit->relative->name, 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, 'رقم السجل الوطني', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, $visit->relative->id_number, 1, 1, 'C');

        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, 'تاريخ اخر زيارة', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, $ar->arNormalizeText($ar->date('Y/m/d', Visit::where('resident_id', $visit->resident_id)->where('relative_id', $visit->relative_id)->latest()->first()->date_time->timestamp), 'Hindu'), 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, 'مدي تواصل الاسرة', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, '', 1, 1, 'C');

        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, 'عدد ايام الزيارة', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, $ar->arNormalizeText($visit->duration . ' ' . Visit::DURATION_TYPE[$visit->duration_type], 'Hindu'), 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, 'رقم التواصل', 1, 0, 'C');
        $this->Cell($this->getPageWidthWithMargins() * 0.25, $cell_height, $visit->relative->phone1, 1, 1, 'C');

        $this->Output(public_path('new.pdf'), 'F');
    }

    protected function getPageWidthWithMargins(): float|int
    {
        return $this->getPageWidth() - 20;
    }

    protected function writeRelationChoices(string $selected)
    {
        $circle_class = 'height: 2px;width:2px;background-color: #0000;border-radius: 50%;display: inline-block;';
        $path = public_path('logo.png');

        $html = '';
        foreach (RelativeResident::RELATION as $item) {
            $html .= '<div style="display: inline">';
            $html .= '<img src="' . $path . '" alt="test alt attribute" width="10" height="10" />';
            $html .= '</div>';

        }

        return $html;
    }
}
