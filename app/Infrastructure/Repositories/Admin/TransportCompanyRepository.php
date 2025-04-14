<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TransportCompany;
use Illuminate\Support\Collection;

class TransportCompanyRepository
{
    public function create(string $code, string $name)
    {
        $existsCompany = TransportCompany::where('code', $code)->first();

        if (!$existsCompany) {
            return TransportCompany::create([
                'code' => $code,
                'name' => $name
            ]);
        }

        return $existsCompany;
    }

    public function getAll(): Collection
    {
       return TransportCompany::get();
    }
}
