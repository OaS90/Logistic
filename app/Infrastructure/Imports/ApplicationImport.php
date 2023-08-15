<?php

namespace App\Infrastructure\Imports;

use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ApplicationImport extends DefaultValueBinder implements WithCustomCsvSettings,
    ImportEntity, WithCustomValueBinder, WithCalculatedFormulas
{
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'windows-1251'
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        // Изменение столбца, если из excel выгрузился по типу 2,1E+10
        // тогда убираем запятую и делаем integer.
        // При изменении столбцов помянять и тут
        // TODO проверить, правильно ли считает или из-за этого ставятся лишние 0
        if (strpos($value, 'E+') !== false) {
            $value = (int) str_replace(',', '', $value);
        }

        return parent::bindValue($cell, $value);
    }
}
