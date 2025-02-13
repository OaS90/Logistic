<?php

namespace App\Infrastructure\Repositories;

use App\Application\DeliveryAddressDTOInterface;
use App\Domain\DTO\DeliveryAddressDTO;
use App\Models\DeliveryAddress;

class DeliveryAddressRepository
{
    public function create(DeliveryAddressDTOInterface $dto): ?DeliveryAddress
    {
        $building = $dto->building ?? null;

        if ($dto->houseBlockFull) {
            $building .= ' ' . $dto->houseBlockFull;
        }

        if ($dto->houseBlock) {
            $building .= $dto->houseBlock;
        }

        return DeliveryAddress::create([
            'city_name' => $dto->cityName ?? null,
            'region_name' => $dto->regionName ?? null,
            'city_fias' => $dto->cityFias,
            'street' => $dto->street ?? null,
            'street_fias' => $dto->streetFias,
            'building' => $building,
            'floor' => $dto->floor,
            'flat' => $dto->flat,
            'entrance' => $dto->entrance,
            'postcode' => $dto->postCode,
            'use_elevator' => $dto->elevator
        ]);
    }
}
