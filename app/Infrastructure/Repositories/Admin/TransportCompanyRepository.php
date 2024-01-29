<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TransportCompany;

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
}