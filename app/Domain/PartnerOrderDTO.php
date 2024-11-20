<?php

namespace App\Domain;

use App\Models\Application;
use App\Models\ApplicationObi;
use App\Models\Product;
use App\Application\DadataService;

class PartnerOrderDTO
{
    private $app;
    protected DadataService $dadataService;

    public function __construct($application)
    {
        $this->app = $application;
        $this->dadataService = new DadataService();
    }

    public function make(): array
    {
        $isObiPartner = config('app.obi_user_id') == $this->app->user_id;
        $address = $this->getAddressInfo($isObiPartner);

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
                    'phone' => $this->app->mobile_phone
                ],
                'address' => $address,
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
                'address' => count($address) > 0 ? $address : [
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

    public function products(Application $app): array
    {
        $products = [];
        $user = $app->user;

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
                'shipmentCode' => $product->shipment_code ?: $user->suffix . '-' . $app->order_number . '-' . $i
            ];
        }

        return $products;
    }

    private function obiProducts(ApplicationObi $app): array
    {
        $products = [];
        $productsForFComment = [];
        $appCost = $app->products_cost;
        $appWeight = $app->order_weight;
        $exceptExtraPaymentsProducts = [];

        foreach ($app->products as $product) {
            $explodedName = explode('- ', $product->name);

            if (is_array($explodedName)) {
                $name = $explodedName[0];
            } else {
                $name = $product->name;
            }

            if (!str_contains($name, 'Доплата') && !str_contains($name, 'Доставка')) {
                $exceptExtraPaymentsProducts[] = $product;
            }
        }

        if (count($exceptExtraPaymentsProducts) > 1) {
            $eachProductCost = round($appCost / count($exceptExtraPaymentsProducts), 2);
            $eachProductWeight = round($appWeight / count($exceptExtraPaymentsProducts), 2);
        } else {
            $eachProductCost = round($appCost, 2);
            $eachProductWeight = round($appWeight, 2);
        }

        foreach ($exceptExtraPaymentsProducts as $index => $product) {
            /* @var Product $product */
            $i = $index + 1;
            $productsForFComment[] = $product->name;
//            $explodedName = explode('- ', $product->name);
//
//            if (is_array($explodedName)) {
//                $name = $explodedName[0];
//                $count = floatval(str_replace(',', '.', trim($explodedName[1])));
//            } else {
//                $name = $product->name;
//                $count = 1;
//            }
//
//            $explodedProductIdName = explode('_', $name);
//            $productId = $explodedProductIdName[0];

//            if ($count > 1) {
//                $eachProductCost = round($eachProductCost / $count);
//            }

            $products[] = [
                'name' => $name, // Товар
                'vendorCode' => '', // Артикул
                'count' => 1, // Количество (временно)
                'cost' => $eachProductCost, // Оценочная стоимость
                'costAfterDiscounts' => $eachProductCost, // Стоимость с учетом скидки
                'VATRate' => 0, // Ставка НДС
                'leftToPay' => 0, // Сумма к получению
                'weight' => $eachProductWeight, // Расчетный вес (кг)
                'setId' => '',
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

        $allProductsString = implode(';', $productsForFComment);

        return [
            'products' => $products,
            'productsComment' => $allProductsString
        ];
    }

    private function getAddressInfo($isObiPartner = false): array
    {
        if ($isObiPartner) {
            $addressInfo = $this->dadataService->getAddress($this->app->delivery_address, 1);
        } else {
            $addressInfo = $this->dadataService->getAddress($this->app->full_address, 1);
        }

        return $this->parseDadataAddress($addressInfo, $isObiPartner);
    }

    private function parseDadataAddress(array $addressInfo, bool $isObiPartner): array
    {
        if (isset($addressInfo[0]) && count($addressInfo[0]) > 0) {
            $cityInfo = [
                'cityName' => $addressInfo[0]['data']['city'] ?? $addressInfo[0]['data']['settlement_with_type'],
                'cityFias' => $addressInfo[0]['data']['city_fias_id'] ?? $addressInfo[0]['data']['settlement_fias_id']
            ];

            if ($isObiPartner) {
                $address = [
                    'regionName' => $addressInfo[0]['data']['region_with_type'],
                    'cityName' => $cityInfo['cityName'],
                    'cityId' => $cityInfo['cityFias'] ?? '', // ФИАС код города/населенного пункта
                    'street' => $addressInfo[0]['data']['street_with_type'] ?? '',
                    'streetId' => $addressInfo[0]['data']['street_fias_id'] ?? '', // ФИАС код улицы
                    'building' => $addressInfo[0]['data']['house'] ?? '',
                    'floor' => '', // необязательно
                    'flat' => '' // необязательно
                ];
            } else {
                $building = $this->app->address->building ?? '';
                $floor = $this->app->address->floor && $this->app->address->flat ? 'этаж ' . $this->app->address->floor : '';
                $flat = $this->app->address->flat ? 'кв. ' . $this->app->address->flat : '';
                $address = [
                    'regionName' => $addressInfo[0]['data']['region_with_type'],
                    'cityName' => $cityInfo['cityName'],
                    'cityId' => $cityInfo['cityFias'] ?? '', // ФИАС код города/населенного пункта
                    'street' => $addressInfo[0]['data']['street_with_type'] ?? '',
                    'streetId' => $addressInfo[0]['data']['street_fias_id'] ?? '', // ФИАС код улицы
                    'building' => $building,
                    'floor' => $floor, // необязательно
                    'flat' => $flat // необязательно
                ];
            }
        } else {
            if ($isObiPartner) {
                $address = [
                    'regionName' => $this->app->delivery_address,
                    'cityName' => '',
                    'cityId' => '', // ФИАС код города/населенного пункта
                    'street' => '',
                    'streetId' => '', // ФИАС код улицы
                    'building' => '',
                    'floor' => '', // необязательно
                    'flat' => '' // необязательно
                ];
            } else {
                $address = [];
            }
        }

        return $address;
    }
}
