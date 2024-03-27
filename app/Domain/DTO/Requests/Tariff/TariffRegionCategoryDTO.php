<?php

namespace App\Domain\DTO\Requests\Tariff;

class TariffRegionCategoryDTO
{
    /**
     * @param int $categoryId
     * @param TariffRegionCategoryPriceDTO[] $prices
     */
    public function __construct(public readonly int $categoryId,
                                public readonly bool $isUse,
                                public readonly array $prices
    )
    {
    }
}