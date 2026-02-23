<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    /**
     * Generate PDF from view
     */
    protected function generatePDF($view, $data, $filename)
    {
        $pdf = Pdf::loadView($view, $data);
        
        // Optional: Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Return PDF for download
        return $pdf->download($filename . '.pdf');
    }
    
    /**
     * Stream PDF in browser
     */
    protected function streamPDF($view, $data, $filename)
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream($filename . '.pdf');
    }
}