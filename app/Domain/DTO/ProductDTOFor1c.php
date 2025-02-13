<?php

namespace App\Domain\DTO;

class ProductDTOFor1c
{
    public function __construct(public readonly string $name,
                                public readonly string $vendorCode,
                                public readonly int $count,
                                public readonly float $cost,
                                public readonly float $costAfterDiscounts,
                                public readonly int $vatRate,
                                public readonly float $leftToPay,
                                public readonly float $weight,
                                public readonly string $setId,
                                public readonly string $brand,
                                public readonly ?string $tnved,
                                public readonly ?string $country,
                                public readonly string $barcode,
                                public readonly float $volume,
                                public readonly float $width,
                                public readonly float $height,
                                public readonly float $depth,
                                public readonly string $shipmentCode
    )
    {
    }
}
