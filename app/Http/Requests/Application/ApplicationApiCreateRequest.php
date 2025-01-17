<?php

namespace App\Http\Requests\Application;

use App\Domain\DTO\ApplicationDTO;
use App\Domain\DTO\DeliveryAddressDTO;
use App\Domain\DTO\ProductDTO;
use App\Domain\DTO\Requests\ApplicationApiCreateDTO;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationApiCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.partnerId' => 'required|string|min:1',
            '*.id' => 'required|string|min:1',
            '*.storeId' => 'required|string|min:1',
            '*.paymentMethod' => 'required|string|min:1',
            '*.deliveryDate' => 'required|date_format:d.m.Y',
            '*.deliveryTimeFrom' => 'required|date_format:H:i|min:1',
            '*.deliveryTimeTo' => 'required|date_format:H:i|min:1',
            '*.buyer' => 'required|array|min:2',
            '*.buyer.fio' => 'required|string|min:1',
            '*.buyer.phone' => 'required|string|min:1',
            '*.address' => 'required|array|min:5',
            '*.address.regionName' => 'required|string|min:1',
            '*.address.cityName' => 'required|string|min:1',
            '*.address.street' => 'required|string|nullable',
            '*.address.building' => 'required|string|min:1',
            '*.address.flat' => 'required|string|min:1',
            '*.products' => 'required|array|min:1',
            '*.products.*.name' => 'required|string|min:1',
            '*.products.*.brand' => 'required|string|min:1',
            '*.products.*.sku' => 'required|string|min:1',
            '*.products.*.tnved' => 'required|string|min:1',
            '*.products.*.barcode' => 'required|string|min:1',
            '*.products.*.count' => 'required|integer|min:1',
            '*.products.*.cost' => 'required|numeric|min:1',
            '*.products.*.VATRate' => 'required|integer|min:10',
            '*.products.*.leftToPay' => 'required|numeric|min:0',
            '*.products.*.weight' => 'required|numeric|min:0',
            '*.products.*.volume' => 'required|numeric|min:0',
            '*.products.*.width' => 'required|numeric|min:0',
            '*.products.*.height' => 'required|numeric|min:0',
            '*.products.*.depth' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле :attribute обязательное.',
        ];
    }

    public function getDTOs(): array
    {
        $data = $this->validated();
        $apps = [];

        foreach ($data as $app) {
            $products = [];
            $address = new DeliveryAddressDTO(
                regionName: $app['address']['regionName'],
                cityName: $app['address']['cityName'],
                street: $app['address']['street'],
                building: $app['address']['building'],
                flat: $app['address']['flat'] ?? null,
            );

            foreach ($app['products'] as $product) {
                $products[] = new ProductDTO(
                    name: $product['name'],
                    brand: $product['brand'] ?? null,
                    sku: $product['sku'],
                    count: $product['count'] ?? 1,
                    cost: floatval($product['cost']),
                    vat: $product['VATRate'] ?? 20,
                    width: floatval($product['width']),
                    height: floatval($product['height']),
                    depth: floatval($product['depth']),
                    volume: floatval($product['volume']),
                    weight: floatval($product['weight']),
                    tnved: $product['tnved'] ?? null,
                    discountCost: null,
                    countryCode: $product['country'] ?? null,
                    barcode: $product['barcode'] ?? null,
                    leftToPay: $product['leftToPay'] ?? 0
                );
            }

            $apps[] = new ApplicationApiCreateDTO(
                new ApplicationDTO(
                    addressDTO: $address,
                    products: $products,
                    orderNumber: $app['id'],
                    paymentType: $app['paymentMethod'],
                    deliveryTime: $app['deliveryTimeFrom'] . '-' . $app['deliveryTimeTo'],
                    deliveryDate: $app['deliveryDate'],
                    clientFullName: $app['buyer']['fio'],
                    clientPhone: $app['buyer']['phone'],
                    comment: $app['comment'] ?? ''
                ),
                partnerId: $app['partnerId'],
                storeId: $app['storeId']
            );
        }

        return $apps;
    }
}
