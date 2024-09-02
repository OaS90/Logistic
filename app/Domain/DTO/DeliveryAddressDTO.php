<?php

namespace App\Domain\DTO;

use App\Application\DeliveryAddressDTOInterface;

class DeliveryAddressDTO implements DeliveryAddressDTOInterface
{
    public function __construct(public readonly string $regionName,
                                public readonly string $cityName,
                                public readonly string $street,
                                public readonly string $building,
                                public readonly ?int $floor = null,
                                public readonly ?string $flat = null,
                                public readonly ?string $cityFias = null,
                                public readonly ?string $streetFias = null,
                                public readonly ?bool $elevator = false,
                                public readonly ?int $entrance = null,
                                public readonly ?int $postCode = null,
                                public readonly ?string $houseBlockFull = null,
                                public readonly ?string $houseBlock = null,
    )
    {
    }

    public function apiRows(array $data): array
    {
        $data['city_name'] = $data['cityName'];
        $data['region_name'] = $data['regionName'];
        $data['city_fias'] = $data['cityId'];
        $data['street_fias'] = $data['streetId'];

        unset($data['cityName']);
        unset($data['regionName']);
        unset($data['cityId']);
        unset($data['streetId']);

        return $data;
    }
}
