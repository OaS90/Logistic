<?php

namespace App\Domain;

class ProductDTO
{
    public function dbRows(array $allRows): array
    {
        return array_slice($allRows, 17);
    }
}
