<?php

namespace App\Infrastructure\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ApplicationObiImport extends DefaultValueBinder implements ImportEntity, WithCustomValueBinder, WithCalculatedFormulas
{
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'windows-1251'
        ];
    }

//    public function bindValue(Cell $cell, $value): bool
//    {
//    }
//        if ($value != null && $value != 1)
//            dd(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
//        // Изменение столбца, если из excel выгрузился по типу 2,1E+10
//        // тогда убираем запятую и делаем integer.
//        // При изменении столбцов помянять и тут
//        // TODO проверить, правильно ли считает или из-за этого ставятся лишние 0
//        if (strpos($value, 'E+') !== false) {
//            $value = (int)str_replace(',', '', $value);
//        }
//
//    }
//
//    public function columnFormats(): array
//    {
//        return [
//            'D2' => NumberFormat::FORMAT_DATE_DMYMINUS,
//        ];
//    }

}
