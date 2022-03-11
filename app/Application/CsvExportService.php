<?php

namespace App\Application;

use App\Domain\CsvEntity;
use Maatwebsite\Excel\Facades\Excel;

class CsvExportService
{
    public function store(CsvEntity $csvEntity)
    {
        // потом переделать под ftp
        Excel::store($csvEntity, $csvEntity->fileName(), 'public');
    }
}
