<?php

namespace App\Domain\DTO\Requests;

use App\Domain\ApplicationDTO;

class ApplicationUICreateRequestDTO
{
    public function __construct(public readonly ApplicationDTO $applicationDTO)
    {
    }
}