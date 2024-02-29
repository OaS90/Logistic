<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\Region;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Collection;

class TariffRepository
{
    public function create(array $data)
    {
        return Tariff::create($data);
    }

    public function updateByFields(Tariff $tariff, array $fields): void
    {
        $tariff->update($fields);
    }

    public function findById(int $id)
    {
        return Tariff::where('id', $id)->first();
    }

    public function findByIdWithRelationships(int $id, array $relationships)
    {
        return Tariff::where('id', $id)
            ->with($relationships)
            ->first();
    }

    public function getAll(): Collection
    {
        return Tariff::get();
    }

    public function saveRegion(Tariff $tariff, Region $region)
    {
        if ($tariff->regions()->where('tariff_region.region_id', $region->id)->doesntExist())
            return $tariff->regions()->save($region);
    }

    public function deleteRegion(Tariff $tariff, int $regionId): int
    {
        return $tariff->regions()->detach($regionId);
    }

    public function delete(Tariff $tariff): void
    {
        $tariff->regions()->detach();
        $tariff->delete();
    }

    public function getLastId(): int
    {
        return Tariff::all()->last()->id;
    }
}