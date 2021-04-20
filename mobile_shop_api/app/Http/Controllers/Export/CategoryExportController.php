<?php

namespace App\Http\Controllers\Export;

use App\Exports\CategoryExampleExport;
use App\Exports\CategoryExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CategoryExportController extends Controller
{
    public function export() 
    {
        // return (new CategoryExport)->forYear(2020);
        return (new CategoryExport)->download('invoices.xlsx');
    }
}
