<?php

namespace App\Domain;

use Carbon\Carbon;

class PartnerOrderDTO
{
    private $app;

    public function __construct($application)
    {
        $this->app = $application;
    }

    public function make(): array
    {
        $products = $this->products($this->app);

        return [
            'id' => $this->app->order_number,
            'paymentMethod' => $this->app->payment_type,
            'comment' => $this->app->comment,
            'deliveryDate' => Carbon::createFromDate($this->app->delivery_date)->format('d.m.Y'),
            'deliveryTimeFrom' => $this->deliveryTime($this->app->delivery_time)[0],
            'deliveryTimeTo' => $this->deliveryTime($this->app->delivery_time)[1],
            'buyer' => [
                'fio' => $this->app->client_name,
                'phone' => $this->app->mobile_phone
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
            'products' => $products
        ];
    }

    public function deliveryTime($time)
    {
        return explode('-', $time);
    }

    public function products($app)
    {
        $products = [];

        foreach ($app->products as $index => $product) {
            $i = $index + 1;
            $products[] = [
                'name' => $product->name, // Товар
                'vendorCode' => $product->sku, // Артикул
                'count' => $product->count, // Количество
                'cost' => $product->cost, // Оценочная стоимость
                'costAfterDiscounts' => $product->discount_cost ?? $product->cost, // Стоимость с учетом скидки
                'VATRate' => $product->vat, // Ставка НДС
                'leftToPay' => $product->left_to_pay, // Сумма к получению
                'weight' => $product->weight, // Расчетный вес (кг)
                'setId' => $product->sku . '_' . $i,
                'brand' => $product->brand, // Бренд
                'tnved' => $product->tnved, // Код ТНВЭД
                'country' => $product->country_code, // код страны происхождения по ОКСМ
                'barcode' => $product->barcode, // EAN
                'volume' => $product->volume, // объем в м2
                'width' => $product->width, // ширина в см
                'height' => $product->height, // высота в м2
                'depth' => $product->depth, // глубина в см
                'shipmentCode' => $app->order_number . '-TL-' . $i
            ];
        }

        return $products;
    }
}
