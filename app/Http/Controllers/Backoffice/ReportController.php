<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('backoffice.index');
    }

    public function earningsReport()
    {
        return view('backoffice.earnings-report');
    }

    public function incomeReport()
    {
        return view('backoffice.income-report');
    }

    public function rentalReport()
    {
        return view('backoffice.rental-report');
    }
}
