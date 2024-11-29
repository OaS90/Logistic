<?php

namespace App\Application;

use App\Infrastructure\Services\Dadata\DadataService;

class DeliveryAddressService
{
    private DadataService $dadataService;

    public function __construct(DadataService $dadataService)
    {
        $this->dadataService = $dadataService;
    }

    public function checkFiasForCityAndStreet($address)
    {
        return $this->dadataService->getCleanAddress($address);
    }
}
