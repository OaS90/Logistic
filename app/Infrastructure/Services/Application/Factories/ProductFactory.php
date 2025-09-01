<?php

namespace App\Infrastructure\Services\Application\Factories;

use App\Domain\DTO\ProductDTOFor1c;
use App\Domain\DTO\ProductObiDTO;
use App\Domain\DTO\ProductDTO;
use App\Domain\DTO\ProductObiDTOFor1c;
use App\Models\Application;
use App\Models\ApplicationObi;
use App\Models\Product;

class ProductFactory
{
    public function makeProductDTO(array $data): ProductDTO
    {
        return new ProductDTO(
            name: $data['name'],
            brand: $data['brand'] ?? null,
            sku: $data['sku'],
            count: $data['count'] ?? 1,
            cost: floatval(str_replace(' ', '',str_replace(',', '.', $data['cost']))),
            vat: $data['vat'] ?? 20,
            width: floatval(str_replace(',', '.', $data['width'])),
            height: floatval(str_replace(',', '.', $data['height'])),
            depth: floatval(str_replace(',', '.', $data['depth'])),
            volume: floatval(str_replace(',', '.', $data['volume'])),
            weight: floatval(str_replace(',', '.', $data['weight'])),
            tnved: $data['tnved'] ? (int) $data['tnved'] : null,
            discountCost: null,
            countryCode: $data['country_code'] ?? null,
            barcode: $data['barcode'] ? (int) $data['barcode'] : null,
            leftToPay: $data['left_to_pay'] ?? 0
        );
    }

    public function makeProductObiDTO(string $productInfo): ProductObiDTO
    {
        return new ProductObiDTO($productInfo);
    }

    public function makeProductObiDTOFor1c(ApplicationObi $app,
                                           string $productInfo,
                                           int $countOfProducts,
                                           int $positionNumber = 1
    ): ProductObiDTOFor1c
    {
        $explodedName = explode('- ', $productInfo);
        $appCost = $app->getTotalCost();
        $appWeight = $app->getTotalWeight();

        if (is_array($explodedName)) {
            $name = $explodedName[0];
        } else {
            $name = $productInfo;
        }

        // TODO проверить, что если один товар, то сумма будет одинаковая
        // как в случае, если есть доплата или есть доплата и не сколько товаров
        $productCost = round($appCost / $countOfProducts, 2);
        $productWeight = round($appWeight / $countOfProducts, 2);
        $explodedProductIdName = explode('_', $name);
        $productId = $explodedProductIdName[0];

        return new ProductObiDTOFor1c(
            name: $name,
            vendorCode: '',
            count: 1,
            cost: $productCost,
            costAfterDiscounts: $productCost, // Стоимость с учетом скидки
            vatRate: 0, // Ставка НДС
            leftToPay: 0, // Сумма к получению
            weight: $productWeight, // Расчетный вес (кг)
            setId: $productId . '_' . $positionNumber,
            brand: '', // Бренд
            tnved: '', // Код ТНВЭД
            country: '', // код страны происхождения по ОКСМ
            barcode: '', // EAN
            volume: 1, // объем в м2
            width: 1, // ширина в см
            height: 1, // высота в м2
            depth: 1, // глубина в см
            shipmentCode: 'OBI-' . $app->order_number . '-' . $positionNumber
        );
    }

    public function makeProductDTOFor1c(Product $product,
                                        string $partnerSuffix,
                                        string $orderNumber,
                                        int $positionNumber = 1
    ): ProductDTOFor1c
    {
        return new ProductDTOFor1c(
            name: $product->name, // Товар
            vendorCode: $product->sku, // Артикул
            count: $product->count, // Количество
            cost: (float)$product->cost, // Оценочная стоимость
            costAfterDiscounts: $product->discount_cost ? (float)$product->discount_cost : (float)$product->cost, // Стоимость с учетом скидки
            vatRate: $product->vat, // Ставка НДС
            leftToPay: $product->left_to_pay, // Сумма к получению
            weight: $product->weight, // Расчетный вес (кг)
            setId: $product->sku . '_' . $positionNumber,
            brand: $product->brand, // Бренд
            tnved: $product->tnved, // Код ТНВЭД
            country: $product->country_code, // код страны происхождения по ОКСМ
            barcode: $product->barcode, // EAN
            volume: (float)$product->volume, // объем в м2
            width: (float)$product->width, // ширина в см
            height: (float)$product->height, // высота в м2
            depth: (float)$product->depth, // глубина в см
            shipmentCode: $partnerSuffix . '-' . $orderNumber . '-' . $positionNumber
        );
    }
}
