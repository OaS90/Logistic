<?php

namespace App\Domain\DTO;

class HruWarehouseDTO
{
    public function __construct(public readonly string $code,
                                public readonly string $name,
                                public readonly int $regionId
    )
    {
    }
}
