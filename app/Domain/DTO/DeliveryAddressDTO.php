<?php

namespace App\Domain\DTO;

use App\Application\DeliveryAddressDTOInterface;

class DeliveryAddressDTO implements DeliveryAddressDTOInterface
{
    public function __construct(public readonly string $regionName,
                                public readonly string $cityName,
                                public readonly ?string $street,
                                public readonly ?string $building,
                                public readonly ?int $floor = null,
                                public readonly ?string $flat = null,
                                public ?string $cityFias = null,
                                public ?string $streetFias = null,
                                public readonly ?bool $elevator = false,
                                public readonly ?int $entrance = null,
                                public readonly ?int $postCode = null,
                                public readonly ?string $houseBlockFull = null,
                                public readonly ?string $houseBlock = null,
    )
    {
    }
}
