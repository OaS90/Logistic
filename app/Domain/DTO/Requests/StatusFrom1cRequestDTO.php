<?php

namespace App\Domain\DTO\Requests;

class StatusFrom1cRequestDTO
{
    public function __construct(public readonly string $orderNumber,
                                public readonly string $lastStatus,
                                public readonly string $statusDateTime
    )
    {
    }
}