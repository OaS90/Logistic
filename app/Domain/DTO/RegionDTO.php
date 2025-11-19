<?php

namespace App\Domain\DTO;

final class RegionDTO
{
    public function __construct(public readonly int $hruRegionId, public readonly string $name)
    {}
}
