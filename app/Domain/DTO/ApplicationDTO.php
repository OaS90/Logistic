<?php

namespace App\Domain\DTO;

use App\Application\ApplicationDTOInterface;
use App\Application\DeliveryAddressDTOInterface;
use App\Domain\ProductDTO;
use Illuminate\Support\Carbon;

class ApplicationDTO implements ApplicationDTOInterface
{
    /**
     * @param ProductDTO[] $products
     */
    public function __construct(public readonly DeliveryAddressDTOInterface $addressDTO,
                                public array $products,
                                public readonly string $orderNumber,
                                public readonly string $paymentType,
                                public readonly string $deliveryTime,
                                public readonly string $deliveryDate,
                                public readonly string $clientFullName,
                                public readonly string $clientPhone,
                                public readonly ?string $comment,
                                public readonly ?string $storeAddress = null,
                                public readonly ?string $deliveryCost = null
    )
    {
    }

    public function addProduct(ProductDTO $product)
    {
        $this->products[] = $product;
    }

    public function apiRows(array $data, int $addressId): array
    {
        $data['client_phone'] = parse_phone($data['buyer']['phone']);
        $data['client_name'] = $data['buyer']['fio'];
        $data['order_number'] = $data['id'];
        $data['delivery_time'] = $data['deliveryTimeFrom'] . '-' . $data['deliveryTimeTo'];
        $data['delivery_date'] = Carbon::parse($data['deliveryDate'])->format('Y-m-d');
        $data['delivery_cost'] = isset($data['delivery_cost']) ? (float) $data['delivery_cost'] : 0;
        $data['payment_type'] = $data['paymentMethod'];
        $data['delivery_address'] = $addressId;
        $data['warehouse_id'] = $data['storeId'];
        unset($data['products']);
        unset($data['address']);
        unset($data['buyer']);
        unset($data['id']);

        return $data;
    }
}
