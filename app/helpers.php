<?php

function parse_phone($phone): string
{
    $phone = preg_replace('/\s+/', '', str_replace(['+', '-', '(', ')'], '', $phone));

    return substr($phone, 1, 10);
}















































