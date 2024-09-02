<?php

namespace App\Infrastructure\Services\Application\Factories;

use App\Domain\DTO\DeliveryAddressDTO;

class DeliveryAddressFactory
{
    public function makeAddressDTO(array $data): DeliveryAddressDTO
    {
        // обрезаем только нужно строки из файла (адрес доставки)
        $data = array_splice($data, 2, 8);

        return new DeliveryAddressDTO(
            regionName: $data['regionName'],
            cityName: $data['cityName'],
            street: $data['street'],
            building:  $data['building'],
            floor:  $data['floor'],
            flat:  $data['flat'],
            cityFias: $data['cityFias'],
            streetFias:  $data['streetFias'],
        );
    }
}