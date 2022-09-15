<?php

namespace App\Application;

class DeliveryAddressService
{
    protected $dadataService;

    public function __construct()
    {
        $this->dadataService = new DadataService();
    }

    public function checkFiasForCityAndStreet($address, $count)
    {
        return $this->dadataService->getAddress($address, $count);
    }
}
