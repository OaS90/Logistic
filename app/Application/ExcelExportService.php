<?php

namespace App\Application;

use App\Domain\ExcelEntity;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportService
{
    public function store(ExcelEntity $csvEntity): void
    {
        Excel::store($csvEntity, $csvEntity->fileName(), 'ftp');
    }

    public function download(ExcelEntity $csvEntity, string $name = 'quotes'): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
       return Excel::download($csvEntity, $name . '.xlsx');
    }
}
