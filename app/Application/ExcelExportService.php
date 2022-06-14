<?php

namespace App\Application;

use App\Domain\ExcelEntity;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportService
{
    public function store(ExcelEntity $csvEntity)
    {
        Excel::store($csvEntity, $csvEntity->fileName(), 'ftp');
    }

    public function download(ExcelEntity $csvEntity): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
       return Excel::download($csvEntity, 'quotes.xlsx');
    }
}
