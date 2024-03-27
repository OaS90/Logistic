<?php

namespace App\Domain\DTO\Requests\Tariff;

class TariffRegionCategoryPriceDTO
{
    public function __construct(public readonly int $price,
                                public readonly int $secondPrice,
                                public readonly int $zoneId,
                                public readonly string $zoneName
    )
    {
    }
}