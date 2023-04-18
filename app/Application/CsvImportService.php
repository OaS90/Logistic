<?php

namespace App\Application;

use App\Domain\ApplicationDTO;
use App\Domain\DeliveryAddressDTO;
use App\Domain\ProductDTO;
use App\Infrastructure\Repositories\WarehouseRepository;
use App\Models\Warehouse;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Infrastructure\Imports\ImportEntity;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Application\ApplicationService;

/**
 *
 */
class CsvImportService
{
    protected $appRepo;
    protected $productRepo;
    protected $addressRepo;
    protected $appService;
    protected $warehouseRepo;

    public function __construct(ApplicationRepository $appRepo,
                                DeliveryAddressRepository $addressRepository,
                                ProductRepository $productRepository,
                                ApplicationService $appService,
                                WarehouseRepository $warehouseRepository
    )
    {
        $this->appRepo = $appRepo;
        $this->productRepo = $productRepository;
        $this->addressRepo = $addressRepository;
        $this->appService = $appService;
        $this->warehouseRepo = $warehouseRepository;
    }


    public function import($file, ImportEntity $entity, $userId)
    {
        $dataFromCsv = $this->getDataArraysWithDbRows($entity, $file);

        foreach ($dataFromCsv as $key => $data) {
            $existApp = $this->appRepo->getByOrderNumber($data['app']['order_number']);
            $data['app']['user_id'] = $userId;

            $data['app']['delivery_time'] = $data['app']['delivery_from'] . '-' . $data['app']['delivery_till'];
            $warehouse = $this->warehouseRepo->findByAddressAndUserId($userId, $data['app']['store_address']);

            if (!$warehouse)
                return response(['message' => 'Не найден склад'], 400);

            if (!$existApp) {
                $address = $this->addressRepo->createFromCsv($data['address']);
                $data['app']['delivery_address'] = $address->id;
                $data['app']['warehouse_id'] = $warehouse->id;
                $existApp = $this->appRepo->create($data['app']);

                foreach ($data['products'] as $dataProduct) {
                    $this->productRepo->create((new ProductDTO())->toArray($existApp->id, $dataProduct));
                }
            } else {
                $data['app']['warehouse_id'] = $warehouse->id;
                $this->appService->checkAppChanges($existApp, $data['app']);
                $this->appService->checkAppProducts($existApp, $data['products']);
            }

            $this->appService->getDeliveryDateFromHru($existApp->order_number, $existApp->address);
        }

        return response(['message' => 'success'], 200);
    }

    /**
     * Формирует массив значений из csv файла и создаёт заявки
     * @param $entity
     * @param $file
     * @return array
     */
    public function getDataArraysWithDbRows($entity, $file): array
    {
        $apps = [];
        $dataFromFile = Excel::toArray($entity, $file)[0];

        // берём значения столбцов из файла и заменяем на значение столбцов и бд
        $rows = (new ApplicationDTO())->dbRowsFromCsv();

        // формируем массив данных [['app', 'address', 'products']]
        // -1 т.к. первый элемент - заголовки из файла
        for ($i = 1; $i <= count($dataFromFile) - 1; $i++) {
            if ($dataFromFile[$i][16]) {
                $appWithDbColumns = array_combine($rows, $dataFromFile[$i]);

                if ($appWithDbColumns['order_number'] == null) {
                    $apps[$i - 1]['products'][] = (new ProductDTO())->dbRows($appWithDbColumns);
                    continue;
                }
                $appWithDbColumns['delivery_date'] = Carbon::createFromFormat('d.m.Y', $appWithDbColumns['delivery_date'])
                    ->format('Y-m-d');

                $apps[$i]['app'] = (new ApplicationDTO())->dbRows($appWithDbColumns);
                $apps[$i]['address'] = (new DeliveryAddressDTO())->dbRows($appWithDbColumns);
                $apps[$i]['products'][] = (new ProductDTO())->dbRows($appWithDbColumns);
            }
        }

        return $apps;
    }
}
