<?php

namespace App\Infrastructure\Repositories;

use App\Models\DeliveryAddress;

class DeliveryAddressRepository
{
    public function create($data)
    {
        return DeliveryAddress::create([
            'city_name' => $data['city'],
            'region_name' => $data['region_with_type'],
            'city_fias' => $data['city_fias_id'],
            'street' => $data['street_with_type'],
            'street_fias' => $data['street_fias_id'],
            'building' => $data['house'] . $data['block_type_full'] . ' ' . $data['block'],
            'floor' => $data['floor'],
            'flat' => $data['flat'],
            'entrance' => $data['entrance'],
            'postcode' => $data['postcode'],
            'use_elevator' => $data['elevator']
        ]);
    }
}
