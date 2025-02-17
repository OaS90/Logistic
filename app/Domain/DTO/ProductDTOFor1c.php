<?php

namespace App\Domain\DTO;

readonly class ProductDTOFor1c
{
    public function __construct(public string $name,
                                public string $vendorCode,
                                public int $count,
                                public float $cost,
                                public float $costAfterDiscounts,
                                public int $vatRate,
                                public float $leftToPay,
                                public float $weight,
                                public string $setId,
                                public string $brand,
                                public ?string $tnved,
                                public ?string $country,
                                public ?string $barcode,
                                public float $volume,
                                public float $width,
                                public float $height,
                                public float $depth,
                                public string $shipmentCode
    )
    {
    }
}
