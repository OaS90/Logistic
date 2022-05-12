<?php

namespace App\Application;

use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\ProductRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;

class ApplicationService
{
    protected $appRepo;
    protected $productRepo;
    protected $codeGenerator;

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

    public function checkAppProducts($app, array $data)
    {
        foreach ($data as $csvProduct) {
            $appProduct = $this->productRepo->getByAppIdSkuBrand($csvProduct['brand'], $csvProduct['sku'], $app->id);
            $csvProduct['cost'] = floatval(str_replace(' ', '', $csvProduct['cost']));
            $csvProduct['discount_cost'] = floatval(str_replace(' ', '', $csvProduct['discount_cost']));
            $csvProduct['volume'] = floatval(str_replace(' ', '', $csvProduct['volume']));

            if (!$appProduct) {
                $app->update(['doc_ver' => $app->doc_ver + 1]);
                $this->productRepo->create($csvProduct);
            } else {
                $csvProduct['app_id'] = $app->id;
                $this->productRepo->update($csvProduct, $appProduct);
            }
        }
    }

    public function makeStickers($app)
    {

        $barcodes = [];

        foreach ($app->products as $product) {
            $barcodes[] = $this->codeGenerator->getBarcode($product->barcode, $this->codeGenerator::TYPE_EAN_13);
        }

        $pdf = PDF::loadView('sticker', ['codes' => $barcodes, 'application' => $app, 'products' => $app->products])
            ->setPaper([30, -30, 280.77, 320.16]);

        return $pdf;
    }
}
