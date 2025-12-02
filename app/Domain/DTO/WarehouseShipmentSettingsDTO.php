<?php

namespace App\Domain\DTO;

final class WarehouseShipmentSettingsDTO
{
    public function __construct(public readonly string $filialCode,
                                public readonly string $transportCompanyCode,
                                public readonly string $departurePointId,
                                public readonly array $settings,

    )
    {
    }
}
