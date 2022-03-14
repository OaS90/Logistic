<?php

namespace App\Application;

use App\Domain\CsvEntity;
use Maatwebsite\Excel\Facades\Excel;

class CsvExportService
{
    public function store(CsvEntity $csvEntity)
    {
        Excel::store($csvEntity, $csvEntity->fileName(), 'ftp');
    }
}
