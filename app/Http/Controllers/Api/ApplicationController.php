<?php

namespace App\Http\Controllers\Api;

use App\Domain\ApplicationDTO;
use App\Domain\DeliveryAddressDTO;
use App\Domain\ProductDTO;
use App\Http\Controllers\Api\Exceptions\JsonParseException;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\UserRepository;
use Barryvdh\DomPDF\PDF;
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

    public function __construct(ApplicationRepository $repository,
                                DeliveryAddressRepository $addressRepo,
                                ProductRepository $productRepo,
                                WarehouseRepository $warehouseRepo,
                                UserRepository $userRepo,
                                ApplicationService $appService
    )
    {
        $this->repo = $repository;
        $this->productRepo = $productRepo;
        $this->addressRepo = $addressRepo;
        $this->warehouseRepo = $warehouseRepo;
        $this->userRepo = $userRepo;
        $this->appService = $appService;
    }

    public function setStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $errors = [];
        $statuses = [];

       $data = json_decode($request->getContent(), true);

       if (!$data || !is_array($data)) {
           $exception = new JsonParseException('Ошибка формата json');

           return response()->json(['message' => $exception->getMessage(), 'success' => false], 500);
       }

       foreach ($data as $item) {
           $newStatus = last($item['statuses'])['status'];

           try {
               $this->repo->updateStatus($item['id'], $newStatus);
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
        $data = $request->all();
        $user = $this->userRepo->getBy1cId($data[0]['userId']);

        foreach ($data as $app) {
            $app['user_id'] = $user->id;
            $warehouse = $this->warehouseRepo->findByStoreId($app['storeId']);

            if (!$warehouse)
                return response(['message' => 'Не найден склад'], 400);

            $app['storeId'] = $warehouse->id;
            $address = $this->addressRepo->createFromCsv((new DeliveryAddressDTO())->apiRows($app['address']));
            $newApp = $this->repo->create((new ApplicationDTO())->apiRows($app, $address->id));

            foreach ($app['products'] as $product) {
                $this->productRepo->create((new ProductDTO())->apiRows($product, $newApp->id));
            }
        }

        return response(['message' => 'success', 'code' => 200], 200);
    }

    public function getSticker($partnerOrderId): \Illuminate\Http\Response
    {
        $app = $this->repo->getByOrderNumber($partnerOrderId);

        if (!$app)
            return response(['message' => 'Не найдена заявка с номером заказа ' . $partnerOrderId], 400);

        $stickers = $this->appService->makeStickers($app);

        return $stickers instanceof PDF ? $stickers->download() : $stickers;
    }

}
