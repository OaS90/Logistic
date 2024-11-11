<?php

namespace App\Http\Resources;

use App\Domain\DTO\ApplicationDTOFor1c;
use App\Domain\DTO\ApplicationObiDTOFor1c;
use App\Domain\DTO\ProductDTOFor1c;
use App\Domain\DTO\ProductObiDTOFor1c;
use Illuminate\Http\Resources\Json\JsonResource;

/* @property ApplicationDTOFor1c[]|ApplicationObiDTOFor1c[] $resource */
class GetOrdersFor1cResource extends JsonResource
{
    public static $wrap = null;
    public function toArray($request): array
    {
        $data = [];

        foreach ($this->resource as $app) {
            $products = [];

            $i = 1;

            foreach ($app->products as $product) {
                /* @var ProductObiDTOFor1c|ProductDTOFor1c $product*/
                $products[] = [
                    'name' => $product->name, // Товар
                    'vendorCode' => $product->vendorCode, // Артикул
                    'count' => $product->count, // Количество (временно)
                    'cost' => $product->cost, // Оценочная стоимость
                    'costAfterDiscounts' => $product->costAfterDiscounts, // Стоимость с учетом скидки
                    'VATRate' => $product->vatRate, // Ставка НДС
                    'leftToPay' => $product->leftToPay, // Сумма к получению
                    'weight' => $product->weight, // Расчетный вес (кг)
                    'setId' => $product->vendorCode . '_' . $i,
                    'brand' => $product->brand, // Бренд
                    'tnved' => $product->tnved, // Код ТНВЭД
                    'country' => $product->country, // код страны происхождения по ОКСМ
                    'barcode' => $product->barcode, // EAN
                    'volume' => $product->volume, // объем в м2
                    'width' => $product->width, // ширина в см
                    'height' => $product->height, // высота в м2
                    'depth' => $product->depth, // глубина в см
                    'shipmentCode' => $product->shipmentCode,
                ];

                $i++;
            }

            if ($app instanceof ApplicationObiDTOFor1c) {
                $address = $app->deliveryAddress ?? [];
            } else {
                $building = $app->deliveryAddress->block ?
                    $app->deliveryAddress->house . ' ' . $app->deliveryAddress->block : $app->deliveryAddress->house;

                $address = $app->deliveryAddress ? [
                    'regionName'=> $app->deliveryAddress->region,
                    'cityName'=> $app->deliveryAddress->city,
                    'cityId'=> $app->deliveryAddress->cityFias, // ФИАС код города/населенного пункта
                    'street'=> $app->deliveryAddress->street,
                    'streetId'=> $app->deliveryAddress->streetFias, // ФИАС код улицы
                    'building'=> $building,
                    'floor'=> $app->deliveryAddress->floor, // необязательно
                    'flat'=> $app->deliveryAddress->flat // необязательно
                ] : [];
            }

            $data[] = [
                'id' => $app->orderNumber,
                'docVer' => $app->docVer,
                'paymentMethod' => $app->paymentMethod,
                'comment' => $app->comment,
                'deliveryDate' => $app->deliveryDate,
                'deliveryTimeFrom' => $app->deliveryTimeFrom,
                'deliveryTimeTo' => $app->deliveryTimeTo,
                'storeID' => $app->storeId,
                'buyer' => [
                    'fio' => $app->clientFullName,
                    'phone' => $app->clientPhone
                ],
                'address' => $address,
                'products' => $products
            ];
        }

        return $data;
    }
}
