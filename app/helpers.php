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















































