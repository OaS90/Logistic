<?php

namespace App\Application;

use App\Domain\ApplicationDTO;
use App\Domain\ApplicationObiDTO;
use App\Domain\DeliveryAddressDTO;
use App\Domain\ProductDTO;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\WarehouseRepository;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Infrastructure\Imports\ImportEntity;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Repositories\ObiProductsRepository;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 *
 */
class CsvImportService
{
    protected ApplicationRepository $appRepo;
    protected ProductRepository $productRepo;
    protected DeliveryAddressRepository $addressRepo;
    protected ApplicationService $appService;
    protected WarehouseRepository $warehouseRepo;
    protected ApplicationObiRepository $applicationObiRepo;
    protected ObiProductsRepository $obiProductsRepo;

    public function __construct(ApplicationRepository $appRepo,
                                DeliveryAddressRepository $addressRepository,
                                ProductRepository $productRepository,
                                ApplicationService $appService,
                                WarehouseRepository $warehouseRepository,
                                ApplicationObiRepository $applicationObiRepo,
                                ObiProductsRepository $obiProductsRepository
    )
    {
        $this->appRepo = $appRepo;
        $this->productRepo = $productRepository;
        $this->addressRepo = $addressRepository;
        $this->appService = $appService;
        $this->warehouseRepo = $warehouseRepository;
        $this->applicationObiRepo = $applicationObiRepo;
        $this->obiProductsRepo = $obiProductsRepository;
    }


    public function import($file, ImportEntity $entity, $userId)
    {
        $dataFromCsv = $this->getDataArraysWithDbRows($entity, $file);

        foreach ($dataFromCsv as $data) {
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
                $this->appService->checkAppChanges($existApp, $data);
            }

            $this->appService->getDeliveryDateFromHru($existApp->order_number, $existApp->address);
        }

        return response(['message' => 'success'], 200);
    }

    public function importObi($file, ImportEntity $entity, $userId)
    {
        $dataFromFile = Excel::toArray($entity, $file)[0];
        $rows = (new ApplicationObiDTO())->dbRowsFromXlsx();

        for ($i = 4; $i <= count($dataFromFile) - 1; $i++) {
            // убираем номер строки из файла (№ п/п)
            unset($dataFromFile[$i][0]);
            $dataFromFile[$i][1] = Carbon::parse(Date::excelToDateTimeObject($dataFromFile[$i][1]))->format('Y-m-d');
            $appWithDbColumns = array_combine($rows, $dataFromFile[$i]);
            $appWithDbColumns['user_id'] = $userId;
            $orderList = explode(';', $appWithDbColumns['order_list']);
            $products = [];

            foreach ($orderList as $product) {
                $productInfo = strlen(trim($product));

                if ($productInfo != 0) {
                    $products[] = trim($product);
                }
            }
            $productsCost = substr(preg_replace('/[^0-9]/', '', $appWithDbColumns['products_cost']), 0, -2);
            $appWithDbColumns['products_cost'] = floatval($productsCost);
            unset($appWithDbColumns['order_list']);
            $newApp = $this->applicationObiRepo->create($appWithDbColumns);

            foreach ($products as $product) {
                $this->obiProductsRepo->create($product, $newApp->id);
            }
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
            // проверяем, что есть товар в этой столбце.
            // даже если это второй товар для одного заказа,
            // то номера заказа не будет, но название товара будет по-любому
            // или если одинаковые номера заказов подряд

            if ($dataFromFile[$i][16]) {
                // проверка кол-ва нужных ячеек. Должно быть 33
                $appWithDbColumns = array_combine($rows, $dataFromFile[$i]);

                if ($appWithDbColumns['order_number'] == null ||
                    (isset($apps[$i - 1]) && $appWithDbColumns['order_number'] == $apps[$i - 1]['app']['order_number'])) {
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
