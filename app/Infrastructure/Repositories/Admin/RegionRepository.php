<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\Region;

class RegionRepository
{
    public function getByHruRegionId(int $regionId)
    {
        return Region::where('region_id', $regionId)->first();
    }
}