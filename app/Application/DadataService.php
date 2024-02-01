<?php

namespace App\Application;

use App\Infrastructure\DadataAdapter;

class DadataService
{
    public function getAddress($address, $count = null)
    {
        return (new DadataAdapter())->getAddress($address, $count);
    }

    public function getCleanAddress(string $address)
    {
        return (new DadataAdapter())->getCleanAddress($address);
    }
}
