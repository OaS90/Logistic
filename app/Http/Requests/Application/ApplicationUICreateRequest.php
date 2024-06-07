<?php

namespace App\Http\Requests\Application;

use App\Domain\DTO\Requests\ApplicationUICreateAddressDTO;
use App\Domain\DTO\Requests\ApplicationUICreateProductDTO;
use App\Domain\DTO\Requests\ApplicationUICreateRequestDTO;
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
            'fields.comment' => 'required|string|min:1',
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
            'products.weight' => 'required|numeric|min:1',
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
            'addresso.city_fias_id' => 'string|min:1|nullable',
            'address.street_with_type' => 'string|min:1|nullable',
            'address.street_fias_id' => 'string|min:1|nullable',
            'address.house' => 'integer|min:1|nullable',
            'address.block_type_full' => 'string|min:1|nullable',
            'address.block' => 'string|min:1|nullable',
        ];


    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            //
        ];
    }

    public function getDTO(): ApplicationUICreateRequestDTO
    {
        $validatedData = $this->validated();

        return new ApplicationUICreateRequestDTO(
            new ApplicationUICreateProductDTO(
                name: $validatedData['products']['name'],
                sku: $validatedData['products']['sku'],
                brand: $validatedData['products']['brand'],
                vat: $validatedData['products']['vat'],
                count: $validatedData['products']['count'],
                cost: $validatedData['products']['cost'],
                width: $validatedData['products']['width'],
                height: $validatedData['products']['height'],
                depth: $validatedData['products']['depth'],
                weight: $validatedData['products']['weight'],
            ),
            new ApplicationUICreateAddressDTO(
                city: $validatedData['address']['city'],
                regionWithType: $validatedData['address']['region_with_type'],
                cityFias: $validatedData['address']['city_fias_id'],
                streetWithType: $validatedData['address']['street_with_type'],
                streetFias: $validatedData['address']['street_fias_id'],
                houseNumber: $validatedData['address']['house'],
                houseBlockFull: $validatedData['address']['block_type_full'],
                houseBlock: $validatedData['address']['block'],
                flat: $validatedData['addressExtraInfo']['flat'],
                floor: $validatedData['addressExtraInfo']['floor'],
                entrance: $validatedData['addressExtraInfo']['entrance'],
                postCode: $validatedData['addressExtraInfo']['postcode'],
                elevator: $validatedData['addressExtraInfo']['elevator']
            ),
        );
    }
}
