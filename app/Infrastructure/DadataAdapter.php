<?php

namespace App\Infrastructure;

use Dadata\DadataClient;

class DadataAdapter
{
    protected DadataClient $dadataClient;

    public function __construct()
    {
        $this->dadataClient = new DadataClient(config('services.dadata.token'), config('services.dadata.secret'));
    }

    public function getAddress($address, $count = 5)
    {
        return $this->dadataClient->suggest('address', $address, $count);
    }

    public function getCleanAddress(string $address)
    {
        return $this->dadataClient->clean('address', $address);
    }
}
