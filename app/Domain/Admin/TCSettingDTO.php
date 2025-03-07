<?php

namespace App\Domain\Admin;

class TCSettingDTO
{
    public function __construct(public readonly int $tcId,
                                public readonly ?int $warehouseId = null,
                                public readonly ?int $filialId = null
    )
    {
    }
}
