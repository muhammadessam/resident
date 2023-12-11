<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use ArPHP\I18N\Arabic;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function index(Request $request, Visit $visit, Arabic $ar)
    {
        $pdf = \PDF::loadView('pdf.visit_form', compact('visit', 'ar'));
        return $pdf->stream($visit->id . now() . '.pdf');
    }
}
