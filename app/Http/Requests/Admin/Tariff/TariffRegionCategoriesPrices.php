<?php

namespace App\Http\Requests\Admin\Tariff;

use App\Domain\DTO\Requests\Tariff\TariffRegionCategoriesPricesDTO;
use App\Domain\DTO\Requests\Tariff\TariffRegionCategoryDTO;
use App\Domain\DTO\Requests\Tariff\TariffRegionCategoryPriceDTO;
use Illuminate\Foundation\Http\FormRequest;

class TariffRegionCategoriesPrices extends FormRequest
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

    public function all($keys = null): array
    {
        $data = parent::all();
        $data['tariffId'] = $this->route('tariffId');
        $data['regionId'] = $this->route('regionId');

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'tariffId' => 'required|numeric|min:1',
            'regionId' => 'required|numeric|min:1',
            'categories' => 'required|array',
            'categories.*.id' => 'required|integer:min:1',
            'categories.*.name' => 'required|string:min:1',
            'categories.*.isUse' => 'required|boolean',
            'categories.*.prices' => 'required|array',
            'categories.*.prices.*.zone' => 'integer:min:1',
            'categories.*.prices.*.price' => 'integer:min:0',
            'categories.*.prices.*.secondPrice' => 'integer:min:0',
        ];
    }

    public function getDTO(): TariffRegionCategoriesPricesDTO
    {
        $categoriesArr = [];
        $categories = $this->get('categories');

        foreach ($categories as $category) {
            $prices = [];

            foreach ($category['prices'] as $zoneName => $priceInfo) {
                $prices[] = new TariffRegionCategoryPriceDTO(
                    price: $priceInfo['price'],
                    secondPrice: $priceInfo['secondPrice'],
                    zoneId: (int) $priceInfo['zone'],
                    zoneName: $zoneName
                );
            }

            $categoryDTO = new TariffRegionCategoryDTO(
                categoryId: $category['id'],
                isUse: $category['isUse'],
                prices: $prices
            );

            $categoriesArr[] = $categoryDTO;
        }

        return new TariffRegionCategoriesPricesDTO(categories: $categoriesArr);
    }
}
