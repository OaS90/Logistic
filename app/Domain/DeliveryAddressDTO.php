<?php

namespace App\Domain;

class DeliveryAddressDTO
{
    public function dbRows(array $allRows): array
    {
        return array_slice($allRows, 9, 8);
    }
}
