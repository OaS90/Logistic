<?php

namespace App\Domain\DTO\Requests;

use App\Domain\DTO\ApplicationDTO;

class ApplicationApiCreateDTO
{
    public function __construct(public readonly ApplicationDTO $app,
                                public readonly string $partnerId,
                                public readonly string $storeId)
    {
    }
}