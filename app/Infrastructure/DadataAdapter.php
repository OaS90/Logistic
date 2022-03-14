<?php

namespace App\Infrastructure;

use Dadata\DadataClient;

class DadataAdapter
{
    private const TOKEN = "233c60b99c5782f690d9d79e01fc3b7deb3e5ad7";
    private const SECRET = "d53e93ccd79201a428663c154ae669efa28064c7";

    public function getAddress($address)
    {
        $dadataClient = new DadataClient(self::TOKEN, self::SECRET);

        return $dadataClient->suggest('address', $address, 5);
    }
}
