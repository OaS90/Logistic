<?php

namespace App\Application;

use App\Domain\ApplicationDTO;
use App\Domain\ApplicationObiDTO;
use App\Domain\DeliveryAddressDTO;
use App\Domain\ProductDTO;
use App\Infrastructure\Admin\Exceptions\CityFiasWrongFormatException;
use App\Infrastructure\Admin\Exceptions\ProductWithoutSkuException;
use App\Infrastructure\Exceptions\PartnerWarehouseNotFoundException;
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
    private array $lastOrderData = [];

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

    /**
     * @throws PartnerWarehouseNotFoundException
     * @throws CityFiasWrongFormatException|ProductWithoutSkuException
     */
    public function import($file, ImportEntity $entity, int $userId, ?int $storeId = null): void
    {
        $dataFromCsv = $this->getDataArraysWithDbRows($entity, $file);

        foreach ($dataFromCsv as $data) {
            $existApp = $this->appRepo->getByOrderNumber($data['app']['order_number']);
            $data['app']['user_id'] = $userId;
            $data['app']['delivery_time'] = $data['app']['delivery_from'] . '-' . $data['app']['delivery_till'];

            if ($storeId) {
                $warehouse = $this->warehouseRepo->findByStoreId($storeId);
            } else {
                $warehouse = $this->warehouseRepo->findByAddressAndUserId($userId, $data['app']['store_address']);
            }

            if (!$warehouse) {
                throw new PartnerWarehouseNotFoundException();
            }

            if (!$existApp) {
                $address = $this->addressRepo->createFromCsv($data['address']);
                $data['app']['delivery_address'] = $address->id;
                $data['app']['warehouse_id'] = $warehouse->id;
                $existApp = $this->appRepo->create($data['app']);

                foreach ($data['products'] as $dataProduct) {
                    // если не заполняют кол-во товаров, ставим 1
                    if (!$dataProduct['count']) {
                        $dataProduct['count'] = 1;
                    }

                    $this->productRepo->create((new ProductDTO())->toArray($existApp->id, $dataProduct));
                }
            } else {
                $data['app']['warehouse_id'] = $warehouse->id;
                $this->appService->checkAppChanges($existApp, $data);
            }

            $this->appService->getDeliveryDateFromHru($existApp->order_number, $existApp->address);
        }
    }

    public function importObi($file, ImportEntity $entity, $userId): void
    {
        $dataFromFile = Excel::toArray($entity, $file)[0];
        $rows = (new ApplicationObiDTO())->dbRowsFromXlsx();

        for ($i = 4; $i <= count($dataFromFile) - 1; $i++) {
            // убираем номер строки из файла (№ п/п)
            unset($dataFromFile[$i][0]);
            $date = is_int($dataFromFile[$i][1]) ? Date::excelToDateTimeObject($dataFromFile[$i][1]) : $dataFromFile[$i][1];
            $dataFromFile[$i][1] = Carbon::parse($date)->format('Y-m-d');
            $appWithDbColumns = array_combine($rows, array_slice($dataFromFile[$i], 0, 25));

            if ($appWithDbColumns['order_number']) {
                $appWithDbColumns['user_id'] = $userId;
//                $orderList = explode(';', $appWithDbColumns['order_list']);
//                $products = [];

//                foreach ($orderList as $product) {
//                    $productInfo = strlen(trim($product));
//
//                    if ($productInfo != 0) {
//                        $products[] = trim($product);
//                    }
//                }

//                $productsCost = substr(preg_replace('/[^0-9]/', '', $appWithDbColumns['products_cost']), 0, -2);

//                $appWithDbColumns['products_cost'] = floatval($productsCost);
                $explodedPhones = explode(',', $appWithDbColumns['phone']);
                $phones = [];

                if (is_array($explodedPhones) && count($explodedPhones) > 1) {
                    foreach ($explodedPhones as $phone) {
                        $phones[] = parse_phone($phone);
                    }

                    $phones = implode(', ', $phones);
                } else {
                    $phones = str_replace(',', '', parse_phone($appWithDbColumns['phone']));
                }
                $appWithDbColumns['phone'] = $phones;
                $orderList = $appWithDbColumns['order_list'];
                unset($appWithDbColumns['order_list']);

                $existApp = $this->applicationObiRepo->getByOrderNumber($appWithDbColumns['order_number']);
                $appWithDbColumns['order_weight'] = parse_to_float($appWithDbColumns['order_weight']);
                $appWithDbColumns['lift_weight_kg'] = parse_to_float($appWithDbColumns['lift_weight_kg']);
                $appWithDbColumns['hand_lift_weight_kg'] = parse_to_float($appWithDbColumns['hand_lift_weight_kg']);
                $appWithDbColumns['transfer_weight'] = parse_to_float($appWithDbColumns['transfer_weight']);
                $appWithDbColumns['transfer_distance'] = parse_to_float($appWithDbColumns['transfer_distance']);
                $appWithDbColumns['cost_of_transportation'] = parse_to_float($appWithDbColumns['cost_of_transportation']);
                $appWithDbColumns['transfer_cost'] = parse_to_float($appWithDbColumns['transfer_cost']);
                $appWithDbColumns['total_delivery_cost'] = parse_to_float($appWithDbColumns['total_delivery_cost']);
                $appWithDbColumns['lift_cost'] = parse_to_float($appWithDbColumns['lift_cost']);
                $appWithDbColumns['products_cost'] = parse_to_float($appWithDbColumns['products_cost']);

                if (!$existApp) {
                    $existApp = $this->applicationObiRepo->create($appWithDbColumns);

//                    foreach ($products as $product) {
                        $this->obiProductsRepo->create($orderList, $existApp->id);
//                    }
                } else {
                    $this->applicationObiRepo->updateByFields($existApp->order_number, $appWithDbColumns);

                    if (!in_array($existApp->status, ['new', 'refusal'])) {
                        $this->applicationObiRepo
                            ->updateByFields($existApp->order_number, ['doc_ver' => $existApp->doc_ver + 1]);
                    }
                }
            }
        }
    }

    /**
     * Формирует массив значений из csv файла и создаёт заявки
     * @param $entity
     * @param $file
     * @return array
     * @throws CityFiasWrongFormatException
     * @throws \Exception
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
            // проверка кол-ва нужных ячеек. Должно быть 33
            $fileData = array_splice($dataFromFile[$i], 0, 33);

            if ($fileData[0]) {
                $this->lastOrderData = [
                    'index' => $i,
                    'order_number' => $fileData[0],
                    'date' => $fileData[13],
                    'timeFrom' => $fileData[14],
                    'timeTo' => $fileData[15]
                ];
            }

            if ($fileData[16]) {
                $appWithDbColumns = array_combine($rows, $fileData);

                // если нет номера заказа, берём последний доступный и привязываем товар к нему
                if (!$appWithDbColumns['order_number']) {
                    $apps[$this->lastOrderData['index']]['products'][] = (new ProductDTO())->dbRows($appWithDbColumns);

                    continue;
                }

                // если текущий номер заказа = предыдущему, засовываем товар в предыдущий заказ
                if (isset($apps[$i - 1]) && $appWithDbColumns['order_number'] == $apps[$i - 1]['app']['order_number']) {
                    $apps[$i - 1]['products'][] = (new ProductDTO())->dbRows($appWithDbColumns);

                    continue;
                }

                $this->isDateExists($appWithDbColumns);
                $this->isTimeExists($appWithDbColumns);

                if ($appWithDbColumns['city_fias'] && strlen($appWithDbColumns['city_fias']) > 40) {
                    throw new CityFiasWrongFormatException('Неверный формат ФИАС города в заявке номер ' . $appWithDbColumns['order_number']);
                }

                $apps[$i]['app'] = (new ApplicationDTO())->dbRows($appWithDbColumns);
                $apps[$i]['address'] = (new DeliveryAddressDTO())->dbRows($appWithDbColumns);
                $apps[$i]['products'][] = (new ProductDTO())->dbRows($appWithDbColumns);
            }
        }

        return $apps;
    }

    /**
     * Если не указано дата доставки, то ставим из последнего записанного
     * @param array $columns
     * @return void
     */
    private function isDateExists(array &$columns): void
    {
        if (!$columns['delivery_date']) {
            $columns['delivery_date'] = Carbon::parse($this->lastOrderData['date'])->format('Y-m-d');
        } else {
            $columns['delivery_date'] = Carbon::parse($columns['delivery_date'])->format('Y-m-d');
        }
    }

    /**
     * Если не указано время доставки, то ставим из последнего записанного
     * @param array $columns
     * @return void
     */
    private function isTimeExists(array &$columns): void
    {
        if (!$columns['delivery_from']) {
            $columns['delivery_from'] = $this->lastOrderData['timeFrom'];
        }

        if (!$columns['delivery_till']) {
            $columns['delivery_till'] = $this->lastOrderData['timeTo'];
        }
    }
}
