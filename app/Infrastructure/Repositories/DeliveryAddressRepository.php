<?php

namespace App\Infrastructure\Repositories;

use App\Domain\DTO\Requests\ApplicationUICreateAddressDTO;
use App\Models\DeliveryAddress;

class DeliveryAddressRepository
{
    public function create(ApplicationUICreateAddressDTO $dto)
    {
        $building = $dto->houseNumber;

        if ($dto->houseBlockFull) {
            $building .= ' ' . $dto->houseBlockFull;
        }

        if ($dto->houseBlock) {
            $building .= $dto->houseBlock;
        }

        return DeliveryAddress::create([
            'city_name' => $dto->city,
            'region_name' => $dto->regionWithType,
            'city_fias' => $dto->cityFias,
            'street' => $dto->streetWithType,
            'street_fias' => $dto->streetFias,
            'building' => $building,
            'floor' => $dto->floor,
            'flat' => $dto->flat,
            'entrance' => $dto->entrance,
            'postcode' => $dto->postCode,
            'use_elevator' => $dto->elevator
        ]);
    }

    public function createFromCsv(array $data)
    {
        return DeliveryAddress::create($data);
    }
}
