<?php

namespace App\Application;

use App\Domain\ProductDTO;
use App\Infrastructure\Admin\Exceptions\ProductWithoutSkuException;
use App\Infrastructure\Api;
use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Infrastructure\Imports\ApplicationImportXlsx;
use App\Infrastructure\Imports\ApplicationObiImport;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Models\DeliveryAddress;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @throws ProductWithoutSkuException
     */
    public function checkAppChanges($app, array $data): void
    {
        if (!in_array($app->status, ['new', 'refusal'])) {
            $data['app']['doc_ver'] = $app->doc_ver + 1;
            $app->update($data['app']);
            $this->checkAppProducts($app, $data['products']);
        }
    }

    /**
     * @throws ProductWithoutSkuException
     */
    private function checkAppProducts($app, array $data): void
    {
        foreach ($data as $csvProduct) {
            if (!$csvProduct['sku'] || !isset($csvProduct['sku'])) {
                throw new ProductWithoutSkuException('У товара ' . $csvProduct['name'] .
                    ' отсутствует артикул в заявке номер ' . $app->order_number);
            }

            $appProduct = $this->productRepo->getByAppIdSkuBrand($csvProduct['sku'], $app->id);

            if (!$appProduct) {
                $this->productRepo->create((new ProductDTO())->toArray($app->id, $csvProduct));
            } else {
                $this->productRepo->update((new ProductDTO())->toArray($app->id, $csvProduct), $appProduct);
            }
        }
    }

    public function makeStickers($app): \Illuminate\Http\Response|\Barryvdh\DomPDF\PDF|Response
    {
        $barcodes = [];
        $user = $app->user;

        for ($i = 1; $i <= count($app->products); $i++) {
            $code = $app->order_number . '-' . $user->suffix . '-' . $i;
            $barcode = $this->codeGenerator->getBarcode($code, $this->codeGenerator::TYPE_CODE_128);
            $barcodes[] = [
                'code' => $code,
                'barcode' => $barcode
            ];
        }

        return PDF::loadView('new-sticker', ['barcodes' => $barcodes, 'application' => $app])
            ->setPaper([0, 0, 350, 160]);
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

    public function getAllApplications(): Collection|array
    {
        $common = $this->appRepo->getAll();
        $obi = $this->applicationObiRepo->getAll();

        return $common->merge($obi);
    }

    /**
     * @param string $extension
     * @param bool $isObiUser
     * @return ApplicationImportCsv|ApplicationImportXlsx|ApplicationObiImport
     */
    public function extensionHandler(string $extension, bool $isObiUser): ApplicationImportXlsx|ApplicationImportCsv|ApplicationObiImport
    {
        switch ($extension) {
            case ('xlsx'):
                if ($isObiUser) {
                    return new ApplicationObiImport();
                } else {
                    return new ApplicationImportXlsx();
                }
            default:
                return new ApplicationImportCsv();
        }
    }
}
