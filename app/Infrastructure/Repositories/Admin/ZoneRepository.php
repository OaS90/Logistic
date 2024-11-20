<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TariffZones;
use Illuminate\Database\Eloquent\Collection;

class ZoneRepository
{
    public function getByCode(string $code): ?TariffZones
    {
        return TariffZones::where('code', $code)->first();
    }

    public function create(array $data): TariffZones
    {
        return TariffZones::create($data);
    }

    public function getAll(): Collection
    {
        return TariffZones::get();
    }
}
