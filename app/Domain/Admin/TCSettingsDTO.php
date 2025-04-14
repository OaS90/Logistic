<?php

namespace App\Domain\Admin;

class TCSettingsDTO
{
    public function __construct(public readonly string $tcName,
                                public readonly int $settingId,
                                public readonly ?string $lastTime,
                                public readonly array $days = [],
                                public readonly bool $enabled = false
    )
    {
    }
}
