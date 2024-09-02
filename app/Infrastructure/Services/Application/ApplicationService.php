<?php

namespace App\Infrastructure\Services\Application;

use App\Application\ApplicationServiceInterface;
use App\Domain\DTO\ApplicationDTO;
use App\Domain\DTO\Requests\ApplicationUICreateRequestDTO;
use App\Domain\Enum\ApplicationStatus;
use App\Domain\ProductDTO;
use App\Infrastructure\Api;
use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Infrastructure\Imports\ApplicationImportXlsx;
use App\Infrastructure\Imports\ApplicationObiImport;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Models\Application;
use App\Models\DeliveryAddress;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Repositories\DeliveryAddressRepository;

class ApplicationService implements ApplicationServiceInterface
{
    private ApplicationRepository $appRepo;
    private ApplicationObiRepository $appObiRepo;
    private ProductRepository $productRepo;
    private BarcodeGeneratorDynamicHTML $codeGenerator;
    private DeliveryAddressRepository $deliveryAddressRepo;
    private int $obiUser;

    public function __construct(ApplicationRepository $appRepo,
                                ApplicationObiRepository $appObiRepo,
                                ProductRepository $productRepo,
                                BarcodeGeneratorDynamicHTML $codeGenerator,
                                DeliveryAddressRepository $deliveryAddressRepo
    )
    {
        $this->appRepo = $appRepo;
        $this->productRepo = $productRepo;
        $this->codeGenerator = $codeGenerator;
        $this->deliveryAddressRepo = $deliveryAddressRepo;
        $this->appObiRepo = $appObiRepo;
        $this->obiUser = config('app.obi_user_id');
    }

    public function checkAppChangesAndUpdate(Application $app, ApplicationDTO $dto, int $warehouseId): void
    {
        if (!in_array($app->status, [ApplicationStatus::NEW, ApplicationStatus::REFUSAL])) {
            //$data['app']['doc_ver'] = $app->doc_ver + 1;
            $this->appRepo->updateByFields($app, [
                'doc_ver' => $app->doc_ver + 1,
                'payment_type' => $dto->paymentType,
                'delivery_date' => $dto->deliveryDate,
                'delivery_cost' => $dto->deliveryCost,
                'delivery_time' => $dto->deliveryTime,
                'warehouse_id' => $warehouseId,
                'comment' => $dto->comment,
                'client_name' => $dto->clientFullName,
                'client_phone' => parse_phone($dto->clientPhone), // переписать в класс парсер
            ]);

            $this->checkAppProducts($app, $dto->products);
        }
    }

    public function makeStickers($app): Response|PdfFile
    {
        $barcodes = [];

        foreach ($app->products as $product) {
            if ($product->barcode) {
                $barcodes[] = $this->codeGenerator->getBarcode($product->barcode, $this->codeGenerator::TYPE_EAN_13);
            } else {
                return response(['message' => 'У товара ' . $product->name . ' отсутствует баркод'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return PDF::loadView('sticker', [
            'codes' => $barcodes,
            'application' => $app,
            'products' => $app->products
        ])->setPaper([30, -30, 280.77, 320.16]);
    }

    // TODO переписать в провайдер.
    public function getDeliveryDateFromHru(Application $app, DeliveryAddress $addressEntity): bool
    {
        $api = new Api(config('app.hru_delivery_url'));
        $deliveryResponse = $api
            ->query('', ['q' => 'DeliveryDateBortUdachi', 'address' => $addressEntity->region_and_city]);

        if ($deliveryResponse && isset($deliveryResponse['date'])) {
            $this->appRepo->updateByFields($app, ['hru_delivery_date' => $deliveryResponse['date']]);
        }

        return false;
    }

    public function getAllApplications(): Collection|array
    {
        $common = $this->appRepo->getAll();
        $obi = $this->appObiRepo->getAll();

        return $common->merge($obi);
    }

    // TODO вынести в сервис csv
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

    /**
     * Через Ui в личном кабинете создаётся только обычный заказ (не ОБИ)
     * и с одним товаром т.к. не доделали функционал для нескольких товаров
     * @param ApplicationUICreateRequestDTO $dto
     * @return void
     */
    public function createFromUI(ApplicationUICreateRequestDTO $dto): void
    {
        $user = Auth::user();
        $newDeliveryAddress = $this->deliveryAddressRepo->create($dto->applicationDTO->addressDTO);
        $existApp = $this->appRepo->getByOrderNumber($dto->applicationDTO->orderNumber);

        if (!$existApp) {
            $newApp = $this->appRepo->create($dto->applicationDTO, $user->id, $newDeliveryAddress->id, $dto->warehouseId);
            $this->productRepo->create($dto->applicationDTO->products[0], $newApp->id);
            $existApp = $newApp;
        } else {
            $this->checkAppChangesAndUpdate($existApp, $dto->applicationDTO, $dto->warehouseId);
        }

        $this->getDeliveryDateFromHru($existApp, $newDeliveryAddress);
    }

    /**
     * @param ProductDTO[] $products
     */
    private function checkAppProducts(Application $app, array $products): void
    {
        foreach ($products as $product) {
            $appProduct = $this->productRepo->getByAppIdSkuBrand($product->sku, $app->id);

            if (!$appProduct) {
                $this->productRepo->create($product, $app->id);
            } else {
                $this->productRepo->update($product, $appProduct);
            }
        }
    }
}