<?php

namespace App\Infrastructure\Services\Tariff\Factories;

use App\Domain\DTO\Tariff\RegionDTO;
use App\Models\Region;

class RegionDTOFactory
{
    public function createFromModel(Region $regionEntity): RegionDTO
    {
        $dto = new RegionDTO();

        return $dto;
    }
}