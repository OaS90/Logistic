<?php

namespace App\Domain\DTO\Requests;

use App\Domain\DTO\ApplicationDTO;

class ApplicationUICreateRequestDTO
{
    public function __construct(public readonly ApplicationDTO $applicationDTO,
                                public readonly ?int $warehouseId = null
    )
    {
    }
}