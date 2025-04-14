<?php

namespace App\Domain\DTO;

class HruFilialDTO
{
    public function __construct(public readonly int $filialId,
                                public readonly string $name,
                                public readonly int $warehouseId,
                                public readonly int $regionId
    )
    {
    }
}
