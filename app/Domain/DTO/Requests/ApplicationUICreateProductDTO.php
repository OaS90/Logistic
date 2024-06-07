<?php

namespace App\Domain\DTO\Requests;

class ApplicationUICreateProductDTO
{
    public function __construct(public readonly string $name,
                                public readonly string $sku,
                                public readonly string $brand,
                                public readonly int $vat,
                                public readonly int $count,
                                public readonly float $cost,
                                public readonly float $width,
                                public readonly float $height,
                                public readonly float $depth,
                                public readonly float $weight,
    )
    {

    }
}