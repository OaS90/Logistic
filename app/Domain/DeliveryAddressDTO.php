<?php

namespace App\Domain;

class DeliveryAddressDTO
{
    public function dbRows(array $allRows): array
    {
        return array_slice($allRows, 2, 7);
    }

    public function apiRows(array $data): array
    {
        $data['city_name'] = $data['cityName'];
        $data['region_name'] = $data['regionName'];
        $data['city_fias'] = $data['cityId'];
        $data['street_fias'] = $data['streetId'];

        unset($data['cityName']);
        unset($data['regionName']);
        unset($data['cityId']);
        unset($data['streetId']);

        return $data;
    }
}
