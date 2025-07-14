<?php

function parse_phone($phone): string
{
    $phone = preg_replace('/\s+/', '', str_replace(['+', '-', '(', ')'], '', $phone));

    return substr($phone, 1, 10);
}

function parse_to_float($value): ?float
{
    $removedSpaces = str_replace(' ', '', $value);

    return (float) str_replace(',', '.', $removedSpaces);
}

function parse_string_symbols(?string $value): ?string
{
    if (!$value) {
        return $value;
    }

    $value = str_replace("\xC2\xA0", ' ', $value);
    $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);

    return trim($value);
}














































