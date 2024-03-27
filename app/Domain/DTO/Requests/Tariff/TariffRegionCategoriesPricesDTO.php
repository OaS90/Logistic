<?php

namespace App\Domain\DTO\Requests\Tariff;

class TariffRegionCategoriesPricesDTO
{
    /**
     * @param TariffRegionCategoryDTO[] $categories
     */
    public function __construct(public readonly array $categories)
    {
    }
}