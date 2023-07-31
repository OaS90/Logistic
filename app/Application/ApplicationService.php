<?php

namespace App\Application;

use App\Domain\ProductDTO;
use App\Infrastructure\Api;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Models\DeliveryAddress;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;

class ApplicationService
{
    protected ApplicationRepository $appRepo;
    protected ProductRepository $productRepo;
    protected BarcodeGeneratorDynamicHTML $codeGenerator;

    public function __construct(ApplicationRepository $appRepo,
                                ProductRepository $productRepo,
                                BarcodeGeneratorDynamicHTML $codeGenerator
    )
    {
        $this->appRepo = $appRepo;
        $this->productRepo = $productRepo;
        $this->codeGenerator = $codeGenerator;
    }

    public function checkAppChanges($app, array $data)
    {
        if ($app->status != 'new' ) {
            $data['doc_ver'] = $app->doc_ver + 1;
            $app->update($data);
        }


        return null;
    }

    public function checkAppProducts($app, array $data): void
    {
        foreach ($data as $csvProduct) {
            $appProduct = $this->productRepo->getByAppIdSkuBrand($csvProduct['sku'], $app->id);
            if (!$appProduct) {
                $app->update(['doc_ver' => $app->doc_ver + 1]);
                $this->productRepo->create((new ProductDTO())->toArray($app->id, $csvProduct));
            } else {
                $this->productRepo->update((new ProductDTO())->toArray($app->id, $csvProduct), $appProduct);
            }
        }
    }

    public function makeStickers($app)
    {
        $barcodes = [];

        foreach ($app->products as $product) {
            if ($product->barcode)
                $barcodes[] = $this->codeGenerator->getBarcode($product->barcode, $this->codeGenerator::TYPE_EAN_13);
            else
                return response(['message' => 'У товара ' . $product->name . ' отсутствует баркод'], 400);
        }

        return PDF::loadView('sticker', ['codes' => $barcodes, 'application' => $app, 'products' => $app->products])
            ->setPaper([30, -30, 280.77, 320.16]);
    }

    public function getDeliveryDateFromHru($appNumber, DeliveryAddress $addressEntity): bool
    {

        $api = new Api(config('app.hru_delivery_url'));
        $deliveryResponse = $api
            ->query('', ['q' => 'DeliveryDateBortUdachi', 'address' => $addressEntity->region_and_city]);

        if ($deliveryResponse && isset($deliveryResponse['date'])) {
             $this->appRepo->updateByFields($appNumber, ['hru_delivery_date' => $deliveryResponse['date']]);
        }

        return false;
    }
}
