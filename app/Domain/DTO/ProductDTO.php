<?php

namespace App\Domain\DTO;

class ProductDTO
{
    /**
     * Последние параметры на данный момент не предусмотрены в форме
     * создания заявки. Возможно в будущем будет доработка.
     */
    public function __construct(public readonly string  $name,
                                public readonly ?string $brand,
                                public readonly string  $sku,
                                public readonly int $count,
                                public readonly float $cost,
                                public readonly int $vat,
                                public readonly float $width,
                                public readonly float $height,
                                public readonly float $depth,
                                public readonly float $volume,
                                public readonly float $weight,
                                public readonly ?int $tnved = null,
                                public readonly ?float $discountCost = null,
                                public readonly ?string $countryCode = null,
                                public readonly ?string $barcode = null,
                                public readonly ?float $leftToPay = null //доплата
    )
    {}
}
