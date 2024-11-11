<?php

namespace App\Domain\DTO;

class DadataCleanAddressDTO
{
    public function __construct(public readonly string $regionFias,
                                public readonly string $region,
                                public readonly ?string $cityFias,
                                public readonly ?string $city,
                                public readonly ?string $streetFias,
                                public readonly ?string $street,
                                public readonly ?string $house,
                                public readonly ?string $block,
                                public readonly ?string $entrance,
                                public readonly ?string $floor,
                                public readonly ?string $flat
    )
    {
    }
}