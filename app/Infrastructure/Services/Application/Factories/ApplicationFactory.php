<?php

namespace App\Infrastructure\Services\Application\Factories;

use App\Domain\DTO\ApplicationDTO;
use App\Domain\DTO\ApplicationDTOFor1c;
use App\Domain\DTO\ApplicationObiDTO;
use App\Domain\DTO\ApplicationObiDTOFor1c;
use App\Domain\DTO\DadataCleanAddressDTO;
use App\Domain\DTO\DeliveryAddressDTO;
use App\Domain\DTO\ProductObiDTOFor1c;
use App\Domain\Enum\DefaultDeliveryTime;
use App\Models\Application;
use App\Models\ApplicationObi;

class ApplicationFactory
{
    public function makeApplicationDTO(array $data,
                                       DeliveryAddressDTO $deliveryAddressDTO,
                                       array $products
    ): ApplicationDTO
    {
        return new ApplicationDTO(
            addressDTO: $deliveryAddressDTO,
            products: $products,
            orderNumber: $data['orderNumber'],
            paymentType: $data['paymentType'],
            deliveryTime: $data['deliveryFrom'] . '-' . $data['deliveryTill'],
            deliveryDate: $data['deliveryDate'],
            clientFullName: $data['clientName'],
            clientPhone: $data['clientPhone'],
            comment: $data['comment'] ?? null,
            storeAddress: $data['storeAddress'],
            deliveryCost: $data['deliveryCost'] ?? 0
        );
    }

    public function makeObiApplicationDTO(array $data): ApplicationObiDTO
    {
        return new ApplicationObiDTO(
            deliveryDate: $data['deliveryDate'],
            deliveryTime: $data['deliveryTime'],
            orderNumber: $data['orderNumber'],
            orderType: $data['orderType'],
            clientName: $data['clientName'],
            phones: $data['phones'],
            deliveryType: $data['deliveryType'],
            deliveryZone: $data['deliveryZone'] ?? '',
            orderList: $data['orderList'],
            orderWeight: $data['orderWeight'],
            deliveryAddress: $data['deliveryAddress'],
            overDeliveryZoneKm: $data['overDeliveryZoneKm'] ?? null,
            liftType: $data['liftType'] ?? null,
            liftFloor: $data['liftFloor'] ?? null,
            handLiftFloor: $data['handLiftFloor'] ?? null,
            handLiftWeightKg: $data['handLiftWeightKg'] ?? null,
            transferDistance: $data['transferDistance'] ?? null,
            transferWeight: $data['transferWeight'] ?? null,
            productsCost: $data['productsCost'] ?? null,
            costOfTransportation: $data['costOfTransportation'] ?? null,
            liftCost: $data['liftCost'] ?? null,
            transferCost: $data['transferCost'] ?? null,
            totalDeliveryCost: $data['totalDeliveryCost'] ?? null,
            comment: $data['comment'] ?? ''
        );
    }

    public function makeApplicationDTOFor1c(Application $app,
                                            array $productDTOs,
                                            DadataCleanAddressDTO $addressDTO,
                                            string $userSuffix
    ): ApplicationDTOFor1c
    {
        return new ApplicationDTOFor1c(
            orderNumber: $app->order_number,
            docVer: $app->doc_ver,
            paymentMethod: $app->payment_type,
            comment: $app->comment ?? '',
            deliveryDate: $app->parsed_delivery_date,
            deliveryTimeFrom: $this->getDeliveryTime($app->delivery_time)[0],
            deliveryTimeTo: $this->getDeliveryTime($app->delivery_time)[1],
            storeId: $app->warehouse->store_id ?? null,
            clientFullName: $app->client_name,
            clientPhone: $app->mobile_phone,
            deliveryAddress: $addressDTO,
            products: $productDTOs,
            userSuffix: $userSuffix
        );
    }

    /**
     * @param ApplicationObi $app
     * @param ProductObiDTOFor1c[] $productDTOs
     * @param array $productsDescription
     * @return ApplicationObiDTOFor1c
     */
    public function makeApplicationObiDTOFor1c(ApplicationObi $app,
                                               array $productDTOs,
                                               DadataCleanAddressDTO $addressDTO ,
                                               array $productsDescription = [],
    ): ApplicationObiDTOFor1c
    {
        $comment = $app->comment . '\n ' . implode(', ', $productsDescription);
        $deliveryTime = $this->getDeliveryTime($app->delivery_time);
        $deliveryTimeFrom = $deliveryTime[0] ? trim($deliveryTime[0]) : DefaultDeliveryTime::FROM;
        $deliveryTimeTo = $deliveryTime[1] ? trim($deliveryTime[1]) : DefaultDeliveryTime::TO;

        return new ApplicationObiDTOFor1c(
            orderNumber: $app->order_number,
            docVer: $app->doc_ver,
            paymentMethod: $app::DEFAULT_PAYMENT_TYPE,
            comment: $comment,
            deliveryDate: $app->parsed_delivery_date,
            deliveryTimeFrom: $deliveryTimeFrom,
            deliveryTimeTo: $deliveryTimeTo,
            storeId: $app::DEFAULT_STORE_ID,
            clientFullName: $app->client_name,
            clientPhone: $app->mobile_phone,
            products: $productDTOs,
            deliveryAddress: $addressDTO,
        );
    }

    private function getDeliveryTime(string $time): array
    {
        if (!$time) {
            $time = '10:00-18:00';
        }

        return explode('-', $time);
    }
}
