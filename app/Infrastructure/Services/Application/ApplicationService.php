<?php

namespace App\Infrastructure\Services\Application;

use App\Application\ApplicationServiceInterface;
use App\Application\DeliveryAddressService;
use App\Domain\DTO\Requests\ApplicationApiCreateDTO;
use App\Domain\DTO\Requests\ApplicationUICreateRequestDTO;
use App\Domain\DTO\Requests\StatusFrom1cRequestDTO;
use App\Http\Controllers\Api\Exceptions\PartnerApplicationsNotFoundException;
use App\Http\Controllers\Api\Exceptions\PartnerNotFoundException;
use App\Http\Controllers\Api\Exceptions\UserNotFoundException;
use App\Http\Controllers\Api\Exceptions\WarehouseNotFoundException;
use App\Infrastructure\Api;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\AppStatusHistoryRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\WarehouseRepository;
use App\Infrastructure\Services\Application\Factories\ApplicationFactory;
use App\Infrastructure\Services\Application\Factories\ProductFactory;
use App\Models\Application;
use App\Models\ApplicationObi;
use App\Models\DeliveryAddress;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Services\Dadata\DadataService;

class ApplicationService implements ApplicationServiceInterface
{
    private int $obiUser;

    public function __construct(private readonly ApplicationRepository $appRepo,
                                private readonly ApplicationObiRepository $appObiRepo,
                                private readonly ProductRepository $productRepo,
                                private readonly BarcodeGeneratorDynamicHTML $codeGenerator,
                                private readonly DeliveryAddressRepository $deliveryAddressRepo,
                                private readonly ApplicationCheckService $appCheckService,
                                private readonly DeliveryAddressRepository $addressRepo,
                                private readonly WarehouseRepository $warehouseRepo,
                                private readonly DeliveryAddressService $deliveryAddressService,
                                private readonly AppStatusHistoryRepository $appStatusHistoryRepo,
                                private readonly UserRepository $userRepo,
                                private readonly ApplicationObiRepository $applicationObiRepo,
                                private readonly ProductFactory $productFactory,
                                private readonly ApplicationFactory $appFactory,
                                private readonly DadataService $dadataService
    )
    {
        $this->obiUser = config('app.obi_user_id');
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
            $this->appCheckService->checkAppChangesAndUpdate($existApp, $dto->applicationDTO, $dto->warehouseId);
        }

        $this->getDeliveryDateFromHru($existApp, $newDeliveryAddress);
    }

    /**
     * @param ApplicationApiCreateDTO[] $DTOs
     * @throws UserNotFoundException
     * @throws WarehouseNotFoundException
     */
    public function createByApi(array $DTOs): array
    {
        $createdApps = [];

        foreach ($DTOs as $appCreateDTO) {
            $user = $this->userRepo->getBy1cId($appCreateDTO->partnerId);
            $appDTO = $appCreateDTO->app;

            if (!$user) {
                throw new UserNotFoundException($appCreateDTO->partnerId);
            }

            $warehouse = $this->warehouseRepo->findByStoreId($appCreateDTO->storeId);

            if (!$warehouse) {
                throw new WarehouseNotFoundException();
            }

            if (!isset($appDTO->addressDTO->regionName) || !isset($appDTO->addressDTO->streetFias)) {
                $dadataAddress = $this->deliveryAddressService
                    ->checkFiasForCityAndStreet(
                        $appDTO->addressDTO->regionName . ' '
                        . $appDTO->addressDTO->cityName . ' '
                        . $appDTO->addressDTO->street
                        , 1
                    );
                $appDTO->addressDTO->cityFias = $dadataAddress ? $dadataAddress[0]['data']['city_fias_id'] : '';
                $appDTO->addressDTO->streetFias = $dadataAddress ? $dadataAddress[0]['data']['street_fias_id'] : '';
            }

            $address = $this->addressRepo->create($appDTO->addressDTO);
            $newApp = $this->appRepo->create($appDTO, $user->id, $address->id, $warehouse->id);

            // записываем историю статусов заказа
            $this->appStatusHistoryRepo->create([
                'number' => $newApp->order_number,
                'status' => 'created',
                'dateTime' => $newApp->created_at
            ]);

            foreach ($appDTO->products as $product) {
                $this->productRepo->create($product, $newApp->id);
            }

            $createdApps[] = $newApp->order_number . '-' . $newApp->id;
        }

        return $createdApps;
    }

    /**
     * @param StatusFrom1cRequestDTO[] $statusDTOs
     * Обновление статусов из 1с
     */
    public function updateStatuses(array $statusDTOs): array
    {
        $errors = [];
        $statuses = [];

        foreach ($statusDTOs as $statusDTO) {
            try {
                $app = $this->appRepo->getByOrderNumber($statusDTO->orderNumber);

                if (!$app) {
                    $app = $this->applicationObiRepo->getByOrderNumber($statusDTO->orderNumber);

                    if ($app) {
                        $this->applicationObiRepo->updateByFields($statusDTO->orderNumber, ['status' => $statusDTO->lastStatus]);
                    } else {
                        Log::error('Не найден заказ Obi с номером ' . $statusDTO->orderNumber);
                    }
                } else {
                    $this->appRepo->updateStatus($statusDTO->orderNumber, $statusDTO->lastStatus);
                }

                if (!$app) {
                    $errors[] = [
                        'id' => $statusDTO->orderNumber,
                        'success' => false,
                        'message' => 'Заявка не найдена.'
                    ];

                    continue;
                }

                // записываем историю обновления статусов заказа
                $this->appStatusHistoryRepo->create([
                    'number' => $statusDTO->orderNumber,
                    'status' => $statusDTO->lastStatus,
                    'dateTime' => $statusDTO->statusDateTime
                ]);

                $statuses[] = [
                    'id' => $statusDTO->orderNumber,
                    'success' => true,
                    'message' => ""
                ];
            } catch (\Throwable $e) {
                $errors[] = [
                    'id' => $statusDTO->orderNumber,
                    'success' => false,
                    'message' => 'Произошла непредвиденная ошибка'
                ];

                // Если ошибка в логике, то просто обновляем версию
                // для того, чтобы 1с пыталась забрать этот заказ в будущем
                $app = $this->appRepo->getByOrderNumber($statusDTO->orderNumber);

                if ($app) {
                    $app->update(['doc_ver' => $app->doc_ver + 1]);
                }
            }
        }

        return array_merge($statuses, $errors);
    }

    /**
     * 1с запрашивает все заявки с определёнными статусами
     * по id партнёра. Пример partnerId 000000025
     * @throws PartnerNotFoundException
     * @throws PartnerApplicationsNotFoundException
     */
    public function getOrdersBy1c(string $partnerId): array
    {
        $user = $this->userRepo->getBy1cId($partnerId);

        if (!$user) {
            throw new PartnerNotFoundException($partnerId);
        }

        $isObiPartner = $this->obiUser == $user->id;

        if ($isObiPartner) {
            $applications = $this->appObiRepo->getListByUserIdForUpdateStatus($user->id);
        } else {
            $applications = $this->appRepo->getListByUserIdForUpdateStatus($user->id);
        }

        if ($applications->count() == 0) {
            throw new PartnerApplicationsNotFoundException($partnerId);
        }

        $apps = [];

        foreach ($applications as $app) {
            $productDTOs = [];
                if ($isObiPartner) {
                    /* @var ApplicationObi $app */
                    $productsForFComment = [];
                    $products = $app->getProductsWithoutExtraPays();

                    foreach ($products as $i => $product) {
                        $i += 1;
                        $productDTOs[] = $this->productFactory
                            ->makeProductObiDTOFor1c($app, $product->name, count($products), $i);
                        $productsForFComment[] = $product->name;
                    }

                    $appDTO = $this->appFactory->makeApplicationObiDTOFor1c($app, $productDTOs, $productsForFComment);
                } else {
                    /* @var Application $app */
                    $addressInfo = $this->dadataService->getCleanAddress($app->full_address);

                    foreach ($app->products as $i => $product) {
                        $i += 1;

                        $productDTOs[] = $this->productFactory
                            ->makeProductDTOFor1c($product, $user->suffix, $app->order_number, $i);
                    }

                    $appDTO = $this->appFactory->makeApplicationDTOFor1c($app, $productDTOs, $addressInfo, $user->suffix);
                }

//                try {
//                    $appDTO = (new PartnerOrderDTO($app))->make();
//                } catch (\Throwable $e) {
//                    Log::info('Error creating dto for app ' . $app->order_number . 'error:' . $e->getMessage());
//                    continue;
//                }

                $apps[] = $appDTO;

                Log::info('Sent to 1c ' . json_encode($appDTO));

                if ($isObiPartner) {
                    $this->appObiRepo->updateByFields($appDTO->orderNumber, ['old_doc_ver' => $appDTO->docVer]);
                } else {
                    $this->appRepo->updateByFields($app, ['old_doc_ver' => $appDTO->docVer]);
                }
//            }
        }

        return $apps;
        return response()->json($apps, 200,
            ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
        );
    }
}
