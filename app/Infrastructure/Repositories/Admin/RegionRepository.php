<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\Region;
use Illuminate\Support\Collection;

class RegionRepository
{
    public function getByHruRegionId(int $regionId)
    {
        return Region::where('region_id', $regionId)->first();
    }

    public function getAll(): Collection
    {
        return Region::all();
    }
}
