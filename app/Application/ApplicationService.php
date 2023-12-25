<?php

namespace App\Application;

use App\Domain\ProductDTO;
use App\Infrastructure\Api;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Models\DeliveryAddress;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;

class ApplicationService
{
    private ApplicationRepository $appRepo;
    private ProductRepository $productRepo;
    private BarcodeGeneratorDynamicHTML $codeGenerator;
    private ApplicationObiRepository $applicationObiRepo;

    public function __construct(ApplicationRepository $appRepo,
                                ApplicationObiRepository $applicationObiRepo,
                                ProductRepository $productRepo,
                                BarcodeGeneratorDynamicHTML $codeGenerator
    )
    {
        $this->appRepo = $appRepo;
        $this->productRepo = $productRepo;
        $this->codeGenerator = $codeGenerator;
        $this->applicationObiRepo = $applicationObiRepo;
    }

    public function checkAppChanges($app, array $data)
    {
        if (!in_array($app->status, ['new', 'refusal'])) {
            $data['app']['doc_ver'] = $app->doc_ver + 1;
            $app->update($data['app']);
            $this->checkAppProducts($app, $data['products']);
        }

        return null;
    }

    private function checkAppProducts($app, array $data): void
    {
        foreach ($data as $csvProduct) {
            $appProduct = $this->productRepo->getByAppIdSkuBrand($csvProduct['sku'], $app->id);

            if (!$appProduct) {
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

        $pdf = PDF::loadView('sticker', ['codes' => $barcodes, 'application' => $app, 'products' => $app->products])
            ->setPaper([30, -30, 280.77, 320.16]);

        return $pdf;
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

    public function getAllApplications(): LengthAwarePaginator
    {
        $commonApps = $this->appRepo->getAll();

        return $commonApps;
    }
}
