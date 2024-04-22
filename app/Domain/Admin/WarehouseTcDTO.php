<?php

namespace App\Domain\Admin;

/*
* DTO для настроек склада и транспортных компаний
*/
class WarehouseTcDTO
{
    public function __construct(public readonly string $name,
                                public readonly string $code,
                                public readonly ?int $regionId
    )
    {
    }
}