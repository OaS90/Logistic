<?php

namespace App\Infrastructure\Imports;

use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use App\Infrastructure\Imports\ImportEntity;

class ApplicationImport implements WithCustomCsvSettings, ImportEntity
{
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'windows-1251'
        ];
    }
}
