<?php

namespace App\Domain;

interface CsvEntity
{
    /**
     * @return string
     */
    public function fileName(): string;
}
