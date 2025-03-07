<?php

namespace App\Domain\DTO\Requests;

use App\Domain\Admin\TCSettingsDTO;

class FilialTCSaveRequestDTO
{
    /**
     * @param TCSettingsDTO[] $settings
     */
    public function __construct(public readonly int $filialId,
                                public readonly string $filialCode,
                                public readonly int $regionId,
                                public readonly int $delayDays,
                                public readonly int $quote,
                                public readonly array $settings
    )
    {
    }
}
