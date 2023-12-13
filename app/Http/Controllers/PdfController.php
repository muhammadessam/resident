<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use ArPHP\I18N\Arabic;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function index(Request $request, Visit $visit, Arabic $ar)
    {
        $last_visit = Visit::where('resident_id', $visit->resident_id)->where('relative_id', $visit->relative_id)->latest('date_time')->skip(1)->first();
        $pdf = \PDF::loadView('pdf.visit_form', compact('visit', 'ar', 'last_visit'));
        return $pdf->stream($visit->id . now() . '.pdf');
    }
}
