<?php

namespace App\Infrastructure\Imports;

use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ApplicationObiImport extends DefaultValueBinder implements ImportEntity,
    WithCustomValueBinder, WithCalculatedFormulas
{

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'windows-1251'
        ];
    }
}
