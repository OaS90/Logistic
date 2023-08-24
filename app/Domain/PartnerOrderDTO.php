<?php

namespace App\Domain;

use App\Application\ApplicationInterface;
use App\Models\Application;
use App\Models\ApplicationObi;
use App\Models\Product;

class PartnerOrderDTO
{
    private $app;

    public function __construct($application)
    {
        $this->app = $application;
    }

    public function make(): array
    {
        $isObiPartner = config('app.obi_user_id') == $this->app->user_id;

        if ($isObiPartner) {
            $productsInfo = $this->obiProducts($this->app);
            $comment = $this->app->comment . '\n ' . $productsInfo['productsComment'];

            $data = [
                'id' => $this->app->order_number,
                'docVer' => $this->app->doc_ver,
                'paymentMethod' => 'Предоплата',
                'comment' => $comment,
                'deliveryDate' => $this->app->parsed_delivery_date,
                'deliveryTimeFrom' => $this->deliveryTime($this->app->delivery_time)[0],
                'deliveryTimeTo' => $this->deliveryTime($this->app->delivery_time)[1],
                'storeID' => 14010,
                'buyer' => [
                    'fio' => $this->app->client_name,
                    'phone' => $this->app->phone
                ],
                'address' => [
                    'regionName'=> $this->app->delivery_address,
                    'cityName'=> '',
                    'cityId'=> '', // ФИАС код города/населенного пункта
                    'street'=> '',
                    'streetId'=> '', // ФИАС код улицы
                    'building'=> '',
                    'floor'=> '', // необязательно
                    'flat'=> '' // необязательно
                ],
                'products' => $productsInfo['products']
            ];
        } else {
            $products = $this->products($this->app);
            $data = [
                'id' => $this->app->order_number,
                'docVer' => $this->app->doc_ver,
                'paymentMethod' => $this->app->payment_type,
                'comment' => $this->app->comment,
                'deliveryDate' => $this->app->parsed_delivery_date,
                'deliveryTimeFrom' => $this->deliveryTime($this->app->delivery_time)[0],
                'deliveryTimeTo' => $this->deliveryTime($this->app->delivery_time)[1],
                'storeID' => $this->app->warehouse->store_id ?? null,
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

        return $data;
    }

    public function deliveryTime($time): array
    {
        return explode('-', $time);
    }

    public function products($app): array
    {
        $products = [];

        foreach ($app->products as $index => $product) {
            /* @var Product $product */
            $i = $index + 1;
            $products[] = [
                'name' => $product->name, // Товар
                'vendorCode' => $product->sku, // Артикул
                'count' => $product->count, // Количество
                'cost' => (float) $product->cost, // Оценочная стоимость
                'costAfterDiscounts' => $product->discount_cost ? (float) $product->discount_cost : (float) $product->cost, // Стоимость с учетом скидки
                'VATRate' => $product->vat, // Ставка НДС
                'leftToPay' => $product->left_to_pay, // Сумма к получению
                'weight' => $product->weight, // Расчетный вес (кг)
                'setId' => $product->sku . '_' . $i,
                'brand' => $product->brand, // Бренд
                'tnved' => $product->tnved, // Код ТНВЭД
                'country' => $product->country_code, // код страны происхождения по ОКСМ
                'barcode' => $product->barcode, // EAN
                'volume' => (float) $product->volume, // объем в м2
                'width' => (float) $product->width, // ширина в см
                'height' => (float) $product->height, // высота в м2
                'depth' => (float) $product->depth, // глубина в см
                'shipmentCode' => 'TL-' . $app->order_number . '-' . $i
            ];
        }

        return $products;
    }

    private function obiProducts(ApplicationObi $app): array
    {
        $products = [];
        $productsForFComment = [];
        $appCost = $app->products_cost;
        $eachProductCost = $appCost / count($app->products);

        foreach ($app->products as $index => $product) {
            /* @var Product $product */
            $i = $index + 1;
            $productsForFComment[] = $product->name;
            $explodedName = explode('-', $product->name);
            $count = 1;

            if (is_array($explodedName)) {
                $name = $explodedName[0];
                $count = $explodedName[1];
            } else {
                $name = $product->name;
            }

            $products[] = [
                'name' => $name, // Товар
                'vendorCode' => '', // Артикул
                'count' => $count, // Количество
                'cost' => $eachProductCost, // Оценочная стоимость
                'costAfterDiscounts' => 0, // Стоимость с учетом скидки
                'VATRate' => 0, // Ставка НДС
                'leftToPay' => 0, // Сумма к получению
                'weight' => 1, // Расчетный вес (кг)
                'setId' => $name . '_' . $i,
                'brand' => '', // Бренд
                'tnved' => '', // Код ТНВЭД
                'country' => '', // код страны происхождения по ОКСМ
                'barcode' => '', // EAN
                'volume' => 1, // объем в м2
                'width' => 1, // ширина в см
                'height' => 1, // высота в м2
                'depth' => 1, // глубина в см
                'shipmentCode' => 'OBI-' . $app->order_number . '-' . $i
            ];
        }

        $allProductsString = implode('; ', $productsForFComment);

        return [
            'products' => $products,
            'productsComment' => $allProductsString
        ];
    }
}
