<?php

namespace App\Infrastructure\Imports;

use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use App\Infrastructure\Imports\ImportEntity;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ApplicationImport extends DefaultValueBinder implements WithCustomCsvSettings, ImportEntity, WithCustomValueBinder
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

        if (strpos($value, 'E+') !== false) {
            $value = (int) str_replace(',', '', $value);
        }

        return parent::bindValue($cell, $value);
    }
}
