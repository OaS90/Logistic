<?php

namespace App\Application;

use App\Infrastructure\DadataAdapter;

class DadataService
{
    public function getAddress($address)
    {
        return (new DadataAdapter())->getAddress($address);
    }
}
