<?php

namespace App\Application;

use App\Domain\DTO\Requests\ApplicationUICreateRequestDTO;
use App\Http\Requests\Application\ApplicationUICreateRequest;

interface ApplicationServiceInterface
{
    public function createFromUI(ApplicationUICreateRequestDTO $dto);
}