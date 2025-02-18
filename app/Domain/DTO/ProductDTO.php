<?php

namespace App\Domain\DTO;

readonly class ProductDTO
{
    /**
     * Последние параметры на данный момент не предусмотрены в форме
     * создания заявки. Возможно в будущем будет доработка.
     */
    public function __construct(public string  $name,
                                public ?string $brand,
                                public string  $sku,
                                public int $count,
                                public float $cost,
                                public int $vat,
                                public float $width,
                                public float $height,
                                public float $depth,
                                public float $volume,
                                public float $weight,
                                public ?int $tnved = null,
                                public ?float $discountCost = null,
                                public ?string $countryCode = null,
                                public ?string $barcode = null,
                                public ?float $leftToPay = null //доплата
    )
    {}
}
