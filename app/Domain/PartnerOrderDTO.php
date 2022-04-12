<?php

namespace App\Domain;

class PartnerOrderDTO
{
    private $app;

    public function __construct($application)
    {
        $this->app = $application;
    }

    public function make(): array
    {
        return [
            'id' => $this->app->id,
            'paymentMethod' => $this->app->payment_type,
            'comment' => $this->app->comment,
            'deliveryDate' => $this->app->delivery_date,
            'deliveryTimeFrom' => $this->deliveryTime($this->app->delivery_time)[0],
            'deliveryTimeTo' => $this->deliveryTime($this->app->delivery_time)[1],
            'buyer' => [
                'fio' => $this->app->client_name,
                'phone' => $this->app->client_phone
            ],
            'address' => [
                'regionName'=> $this->app->address->region_name,
                'cityName'=> $this->app->address->city_name,
                'cityId'=> $this->app->address->city_fias, // ФИАС код города/населенного пункта
                'street'=> $this->app->address->street,
                'streetId'=> $this->app->address->street_fias, // ФИАС код улицы
                'building'=> $this->app->address->building,
                'floor'=> $this->app->address->floor, // необязательно
                'flat'=> $this->app->address->flat // необязательно

            ],
            'products' => [
                [
                    'name' => $this->app->product_name, // Товар
                    'vendorCode' => $this->app->product_art, // Артикул
                    'count' => $this->app->count, // Количество
                    'cost' => $this->app->cost, // Оценочная стоимость
                    'costAfterDiscounts' => 10000, // Стоимость с учетом скидки
                    'VATRate' => $this->app->vat, // Ставка НДС
                    'leftToPay' => 0, // Сумма к получению
                    'weight' => $this->app->weight, // Расчетный вес (кг)
                    'setId' => $this->app->product_art . '_1',
                    'brand' => $this->app->product_brand, // Бренд
                    'tnved' => '8516609000', // Код ТНВЭД
                    'country' => '643', // код страны происхождения по ОКСМ
                    'barcode' => '8699272141521', // EAN
                    'volume' => $this->app->volume, // объем в м2
                    'width' => $this->app->width, // ширина в см
                    'height' => $this->app->height, // высота в м2
                    'depth' => $this->app->depth, // глубина в см
                    'shipmentCode' => $this->app->id . '-LG-1'
                ]
            ]
        ];
    }

    public function deliveryTime($time)
    {
        return explode('-', $time);
    }

    public function products()
    {

    }
}
