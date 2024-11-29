<?php

namespace App\Http\Requests\Application;

use App\Domain\DTO\ApplicationDTO;
use App\Domain\DTO\DeliveryAddressDTO;
use App\Domain\DTO\Requests\ApplicationUICreateRequestDTO;
use App\Domain\DTO\ProductDTO;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationUICreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // only allow updates if the user is logged in
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'fields' => 'required|array|min:1',
            'fields.order_number' => 'required|string|min:1',
            'fields.payment_type' => 'required|string|min:1|in:Оплата наличными,Онлайн оплата,Оплата на дому',
            'fields.delivery_from' => ['required', 'regex:/([01]?[0-9]|2[0-3]):[0-5][0-9]/'],
            'fields.delivery_till' => ['required', 'regex:/([01]?[0-9]|2[0-3]):[0-5][0-9]/'],
            'fields.delivery_date' => 'required|date:Y-m-d',
            'fields.warehouse_id' => 'required|integer|min:1',
            'fields.client_name' => 'required|string|min:1',
            'fields.client_phone' => ['required', 'regex:/\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}/'],
            'fields.comment' => 'string|min:1',
            'products' => 'required|array|min:1',
            'products.name' => 'required|string|min:1',
            'products.sku' => 'required|string|min:1',
            'products.brand' => 'required|string|min:1',
            'products.vat' => 'required|integer|min:0|in:0,10,20',
            'products.volume' => 'required|numeric',
            'products.cost' => 'required|integer|min:1',
            'products.width' => 'required|numeric|min:1',
            'products.height' => 'required|numeric|min:1',
            'products.depth' => 'required|numeric|min:1',
            'products.weight' => 'required|numeric|min:0',
            'products.count' => 'required|integer|min:1',
            'addressExtraInfo' => 'required|array|min:1',
            'addressExtraInfo.flat' => 'string|min:1',
            'addressExtraInfo.floor' => 'integer|min:1',
            'addressExtraInfo.entrance' => 'integer|min:1',
            'addressExtraInfo.postcode' => 'integer|min:1',
            'addressExtraInfo.elevator' => 'bool',
            'address' => 'required|array|min:1',
            'address.city' => 'required|string|min:1|nullable',
            'address.region_with_type' => 'required|string|min:1|nullable',
            'address.city_fias_id' => 'string|min:1|nullable',
            'address.street_with_type' => 'string|min:1|nullable',
            'address.street_fias_id' => 'string|min:1|nullable',
            'address.house' => 'string|min:1|nullable',
            'address.block_type_full' => 'string|min:1|nullable',
            'address.block' => 'string|min:1|nullable',
        ];
    }

    public function getDTO(): ApplicationUICreateRequestDTO
    {
        $validatedData = $this->validated();

        return new ApplicationUICreateRequestDTO(
            new ApplicationDTO(
                new DeliveryAddressDTO(
                    regionName: $validatedData['address']['region_with_type'],
                    cityName: $validatedData['address']['city'],
                    street: $validatedData['address']['street_with_type'],
                    building: $validatedData['address']['house'],
                    floor: $validatedData['addressExtraInfo']['floor'],
                    flat: $validatedData['addressExtraInfo']['flat'],
                    cityFias: $validatedData['address']['city_fias_id'],
                    streetFias: $validatedData['address']['street_fias_id'],
                    elevator: $validatedData['addressExtraInfo']['elevator'],
                    entrance: $validatedData['addressExtraInfo']['entrance'],
                    postCode: $validatedData['addressExtraInfo']['postcode'],
                    houseBlockFull: $validatedData['address']['block_type_full'],
                    houseBlock: $validatedData['address']['block']
                ),
                [new ProductDTO(
                    name: $validatedData['products']['name'],
                    brand: $validatedData['products']['brand'],
                    sku: $validatedData['products']['sku'],
                    count: $validatedData['products']['count'],
                    cost: $validatedData['products']['cost'],
                    vat: $validatedData['products']['vat'],
                    width: $validatedData['products']['width'],
                    height: $validatedData['products']['height'],
                    depth: $validatedData['products']['depth'],
                    volume: $validatedData['products']['volume'],
                    weight: $validatedData['products']['weight']
                )],
                orderNumber: $validatedData['fields']['order_number'],
                paymentType: $validatedData['fields']['payment_type'],
                deliveryTime: $validatedData['fields']['delivery_from'] . '-' . $validatedData['fields']['delivery_till'],
                deliveryDate: $validatedData['fields']['delivery_date'],
                clientFullName: $validatedData['fields']['client_name'],
                clientPhone: $validatedData['fields']['client_phone'],
                comment: $validatedData['fields']['comment'] ?? null,
                deliveryCost: null
            ),
            $validatedData['fields']['warehouse_id']
        );
    }
}
