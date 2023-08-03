<?php

namespace App\Http\Controllers\Api;

use App\Application\DeliveryAddressService;
use App\Domain\ApplicationDTO;
use App\Domain\DeliveryAddressDTO;
use App\Domain\ProductDTO;
use App\Http\Controllers\Api\Exceptions\JsonParseException;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\AppStatusHistoryRepository;
use App\Infrastructure\Repositories\UserRepository;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Exceptions\StatusUpdateException;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\WarehouseRepository;
use App\Application\ApplicationService;

class ApplicationController
{
    protected $repo;
    protected $addressRepo;
    protected $productRepo;
    protected $warehouseRepo;
    protected $userRepo;
    protected $appService;
    protected $appRepo;
    protected $appStatusHistoryRepo;
    protected $deliveryAddressService;

    public function __construct(ApplicationRepository $repository,
                                DeliveryAddressRepository $addressRepo,
                                ProductRepository $productRepo,
                                WarehouseRepository $warehouseRepo,
                                UserRepository $userRepo,
                                ApplicationService $appService,
                                ApplicationRepository $appRepo,
                                AppStatusHistoryRepository $appStatusHistoryRepo,
                                DeliveryAddressService $deliveryAddressService
    )
    {
        $this->repo = $repository;
        $this->productRepo = $productRepo;
        $this->addressRepo = $addressRepo;
        $this->warehouseRepo = $warehouseRepo;
        $this->userRepo = $userRepo;
        $this->appService = $appService;
        $this->appRepo = $appRepo;
        $this->appStatusHistoryRepo = $appStatusHistoryRepo;
        $this->deliveryAddressService = $deliveryAddressService;
    }

    public function setStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $errors = [];
        $statuses = [];

       $data = json_decode($request->getContent(), true);
       // TODO сделать проверку, что отправляют не массив массивов заказ, а просто заказ
       if (!$data || !is_array($data)) {
           $exception = new JsonParseException('Ошибка формата json');

           return response()->json(['message' => $exception->getMessage(), 'success' => false], 500);
       }

       foreach ($data as $item) {
           // из 1с может приходить история статусов, если апи партнёра не отвечала
           // из-за этого берём последний(актуальный) статус
           $newStatus = last($item['statuses']);

           try {
               $this->repo->updateStatus($item['id'], $newStatus['status']);

               // записываем историю обновления статусов заказа
               $this->appStatusHistoryRepo->create([
                   'number' => $item['id'],
                   'status' => $newStatus['status'],
                   'dateTime' => $newStatus['dateTime']
               ]);

               $statuses[] = [
                   'id' => $item['id'],
                   'success' => true,
                   'message' => ""
               ];
           } catch (\Throwable $e) {
               $exception = new StatusUpdateException();
               $errors[] = $exception->getError($e, $item['id']);
               $app = $this->repo->getByOrderNumber($item['id']);

               if ($app)
                   $app->update(['doc_ver' => $app->doc_ver + 1]);
           }
       }

       return response()->json(array_merge($statuses, $errors));
    }

    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $app) {
            $user = $this->userRepo->getBy1cId($app['partnerId']);

            if (!$user)
                return response()
                    ->json(
                        ['message' => 'Не найден пользователь с идентификаторм ' . $app['partnerId']], 500,
                        ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
                    );

            $app['user_id'] = $user->id;
            $warehouse = $this->warehouseRepo->findByStoreId($app['storeId']);

            if (!$warehouse)
                return response()
                    ->json(
                        ['message' => 'Не найден склад'], 500,
                        ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
                    );

            $app['storeId'] = $warehouse->id;

            if (!isset($app['address']['cityId']) || !isset($app['address']['streetId'])) {
                $dadataAddress = $this->deliveryAddressService
                    ->checkFiasForCityAndStreet($app['address']['regionName'] . ' '. $app['address']['cityName'] . ' ' .$app['address']['street'] , 1);

                $app['address']['cityId'] = $dadataAddress ? $dadataAddress[0]['data']['city_fias_id'] : '';
                $app['address']['streetId'] = $dadataAddress && $dadataAddress[0]['data']['street_fias_id'] ? $dadataAddress[0]['data']['street_fias_id'] : '';
            }

            $address = $this->addressRepo->createFromCsv((new DeliveryAddressDTO())->apiRows($app['address']));
            $newApp = $this->repo->create((new ApplicationDTO())->apiRows($app, $address->id));

            // записываем историю статусов заказа
            $this->appStatusHistoryRepo->create([
                'number' => $newApp->order_number,
                'status' => 'created',
                'dateTime' => $newApp->created_at
            ]);

            foreach ($app['products'] as $product) {
                $this->productRepo->create((new ProductDTO())->apiRows($product, $newApp->id));
            }
        }

        return response(['code' => $newApp->order_number . '-' . $newApp->id, 'success' => true, 'message' => ''], 200);
    }

    public function getSticker($partnerOrderId)
    {
        $app = $this->repo->getByOrderNumber($partnerOrderId);

        if (!$app)
            return response()
                ->json(
                    ['message' => 'Не найдена заявка с номером заказа ' . $partnerOrderId], 500,
                    ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
                );

        $stickers = $this->appService->makeStickers($app);

        return $stickers instanceof PDF ? $stickers->download() : $stickers;
    }

    public function getOrderStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $order = $this->userRepo->getOrderByNumberAndUser1cId(
                $request->get('partnerId'),
                $request->get('orderId')
            );
        } catch (\Throwable $e) {
            return response()
                ->json(
                    ['message' => 'Не удалось найти заказ с номер ' . $request->get('orderId')], 500,
                    ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
                );
        }

        return response()->json([
            'message' => 'success',
            'orderNumber' => $order->order_number,
            'orderStatus' => $order->status
        ]);
    }

    public function statusHistory(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $statuses = $this->appStatusHistoryRepo->getByFewOrders($data['ids']);

            if (count($statuses) == 0)
                $statuses = ['message' => 'История статусов для заказа(ов) пуста'];
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Ошибка получения истории'], 500);
        }

        return response()->json($statuses);
    }
}
