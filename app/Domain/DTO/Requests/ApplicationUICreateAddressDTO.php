<?php

namespace App\Domain\DTO\Requests;

class ApplicationUICreateAddressDTO
{
    public function __construct(public readonly string $city,
                                public readonly string $regionWithType,
                                public readonly ?string $cityFias,
                                public readonly ?string $streetWithType,
                                public readonly ?string $streetFias,
                                public readonly ?int $houseNumber,
                                public readonly ?string $houseBlockFull,
                                public readonly ?string $houseBlock,
                                public readonly ?string $flat,
                                public readonly ?int $floor,
                                public readonly ?int $entrance,
                                public readonly int $postCode,
                                public readonly bool $elevator
    )
    {
    }
}