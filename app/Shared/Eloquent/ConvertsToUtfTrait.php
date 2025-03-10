<?php

namespace App\Shared\Eloquent;

trait ConvertsToUtfTrait
{
    public function toUtf($value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return mb_convert_encoding($value, 'UTF-8', 'Windows-1251');
    }

    public function toCp1251($value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return mb_convert_encoding($value, 'Windows-1251', 'UTF-8');
    }
}
