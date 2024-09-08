<?php

namespace App\Infrastructure\Services\Import;

use App\Domain\DTO\ApplicationDTO;
use App\Domain\Enum\DefaultDeliveryTime;
use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Infrastructure\Imports\ApplicationImportXlsx;
use App\Infrastructure\Imports\ApplicationObiImport;
use App\Infrastructure\Services\Application\ApplicationService;
use App\Infrastructure\Admin\Exceptions\CityFiasWrongFormatException;
use App\Infrastructure\Exceptions\PartnerWarehouseNotFoundException;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\WarehouseRepository;
use App\Infrastructure\Services\Application\Factories\ApplicationFactory;
use App\Infrastructure\Services\Application\Factories\DeliveryAddressFactory;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Infrastructure\Imports\ImportEntity;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Repositories\ObiProductsRepository;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Infrastructure\Services\Application\Factories\ProductFactory;
use App\Infrastructure\Services\Application\ApplicationCheckService;

class CsvImportService
{
    private string $lastOrderNumber;

    public function __construct(private readonly ApplicationRepository $appRepo,
                                private readonly DeliveryAddressRepository $addressRepo,
                                private readonly ProductRepository $productRepo,
                                private readonly ApplicationService $appService,
                                private readonly WarehouseRepository $warehouseRepo,
                                private readonly ApplicationObiRepository $applicationObiRepo,
                                private readonly ObiProductsRepository $obiProductsRepo,
                                private readonly ProductFactory $productFactory,
                                private readonly DeliveryAddressFactory $addressFactory,
                                private readonly ApplicationFactory $appFactory,
                                private readonly ApplicationCheckService $appCheckService
    )
    {}

    /**
     * @throws PartnerWarehouseNotFoundException
     * @throws CityFiasWrongFormatException
     */
    public function import($file, ImportEntity $entity, int $userId, ?int $storeId = null): void
    {
        $dataFromCsv = $this->getDataArraysWithDbRows($entity, $file);
        $warehouse = null;

        // При загрузке из админки диспетчерами, есть id склада
        if ($storeId) {
            $warehouse = $this->warehouseRepo->findByStoreId($storeId);
        }

        foreach ($dataFromCsv as $orderNumber => $appDTO) {
            /* @var ApplicationDTO $appDTO */
            $existApp = $this->appRepo->getByOrderNumber($orderNumber);

            if (!$warehouse) {
                $warehouse = $this->warehouseRepo->findByAddressAndUserId($userId, $appDTO->storeAddress);
            }

            if (!$warehouse) {
                throw new PartnerWarehouseNotFoundException();
            }

            if (!$existApp) {
                $address = $this->addressRepo->create($appDTO->addressDTO);
                $existApp = $this->appRepo->create($appDTO, $userId, $address->id, $warehouse->id);

                foreach ($appDTO->products as $productDTO) {
                    $this->productRepo->create($productDTO, $existApp->id);
                }
            } else {
                $this->appCheckService->checkAppChangesAndUpdate($existApp, $appDTO, $warehouse->id);
            }

            $this->appService->getDeliveryDateFromHru($existApp, $existApp->address);
        }
    }

    public function importObi($file, ImportEntity $entity, $userId): void
    {
        $dataFromFile = Excel::toArray($entity, $file)[0];
        $rows = $this->fileObiTitlesToDbColumnsPrepare();

        for ($i = 4; $i <= count($dataFromFile) - 1; $i++) {
            // убираем номер строки из файла (№ п/п)
            unset($dataFromFile[$i][0]);
            $dataFromFile[$i][1] = Carbon::parse(Date::excelToDateTimeObject($dataFromFile[$i][1]))->format('Y-m-d');
            $appWithDbColumns = array_combine($rows, $dataFromFile[$i]);

            if ($appWithDbColumns['orderNumber']) {
                $orderList = explode(';', $appWithDbColumns['orderList']);
                $products = [];

                // в новых файлах не было разделения через ';'
                // поэтому, если разделения нет, пишем целиком
                if (is_array($orderList)) {
                    foreach ($orderList as $product) {
                        $productInfo = strlen(trim($product));

                        if ($productInfo != 0) {
                            $products[] = $this->productFactory->makeProductObiDTO(trim($product));
                        }
                    }
                } else {
                    $products[] = $this->productFactory->makeProductObiDTO($orderList);
                }

                $productsCost = substr(preg_replace('/[^0-9]/', '', $appWithDbColumns['productsCost']), 0, -2);
                $appWithDbColumns['productsCost'] = floatval($productsCost);
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

                $appWithDbColumns['phones'] = $phones;
                $appWithDbColumns['orderList'] = $products;
                $appDTO = $this->appFactory->makeObiApplicationDTO($appWithDbColumns);
                $existApp = $this->applicationObiRepo->getByOrderNumber($appDTO->orderNumber);

                if (!$existApp) {
                    $existApp = $this->applicationObiRepo->create($appDTO, $userId);

                    foreach ($appDTO->orderList as $product) {
                        $this->obiProductsRepo->create($product, $existApp->id);
                    }
                } else {
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
     */
    public function getDataArraysWithDbRows($entity, $file): array
    {
        $apps = [];
        $dataFromFile = Excel::toArray($entity, $file)[0];

        // берём значения столбцов из файла и заменяем на значение столбцов из бд
        $rows = $this->fileTitlesToDbColumnsPrepare();

        // Формируем массив заявок
        // -1 т.к. первый элемент - заголовки из файла
        for ($i = 1; $i <= count($dataFromFile) - 1; $i++) {
            // Проверяем, что есть товар в этом столбце.
            // Даже если это второй товар для одного заказа,
            // то номера заказа не будет, но название товара будет по-любому
            // или если одинаковые номера заказов подряд
            // проверка кол-ва нужных ячеек. Должно быть 33
            $fileData = array_splice($dataFromFile[$i], 0, 33);

            if ($fileData[0]) {
                $this->lastOrderNumber = $fileData[0];
            }

            if ($fileData[16]) {
                $appWithDbColumns = array_combine($rows, $fileData);
                // если нет номера заказа, берём последний доступный и привязываем товар к нему
                if (!$appWithDbColumns['orderNumber'] || isset($apps[$appWithDbColumns['orderNumber']])) {
                    /* @var ApplicationDTO $app */
                    $app = $apps[$this->lastOrderNumber];
                    $product = $this->productFactory->makeProductDTO($appWithDbColumns);
                    $app->addProduct($product);

                    continue;
                }

                $this->isDateExists($appWithDbColumns);
                $this->isTimeExists($appWithDbColumns);

                if ($appWithDbColumns['cityFias'] && strlen($appWithDbColumns['cityFias']) > 40) {
                    throw new CityFiasWrongFormatException('Неверный формат ФИАС города в заявке номер ' . $appWithDbColumns['order_number']);
                }

                $address = $this->addressFactory->makeAddressDTO($appWithDbColumns);
                $product = $this->productFactory->makeProductDTO($appWithDbColumns);
                $app = $this->appFactory->makeApplicationDTO($appWithDbColumns, $address, [$product]);
                $apps[$appWithDbColumns['orderNumber']] = $app;
            }
        }

        return $apps;
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

    /**
     * Если не указано дата доставки, то ставим из последнего записанного
     * @param array $columns
     * @return void
     */
    private function isDateExists(array &$columns): void
    {
        if (!isset($columns['deliveryDate'])) {
            $columns['deliveryDate'] = Carbon::now()->addDays(5)->format('Y-m-d');
        } else {
            $columns['deliveryDate'] = Carbon::parse($columns['deliveryDate'])->format('Y-m-d');
        }
    }

    /**
     * Если не указано время доставки, то ставим из последнего записанного
     * @param array $columns
     * @return void
     */
    private function isTimeExists(array &$columns): void
    {
        if (!$columns['deliveryFrom']) {
            $columns['deliveryFrom'] = DefaultDeliveryTime::FROM;
        }

        if (!$columns['deliveryTill']) {
            $columns['deliveryTill'] = DefaultDeliveryTime::TO;
        }
    }

    private function fileTitlesToDbColumnsPrepare(): array
    {
        return [
            "Номер заказа" => 'orderNumber',
            "Адрес склада" => 'storeAddress',
            "Регион" => 'regionName',
            "Город" => 'cityName',
            "ФИАС города" => 'cityFias',
            "Улица" => 'street',
            "ФИАС улицы" => 'streetFias',
            "Дом" => 'building',
            "Этаж" => 'floor',
            "Квартира" => 'flat',
            "ФИО" => 'clientName',
            "Мобильный телефон" => 'clientPhone',
            "Комментарий" => 'comment',
            "Дата доставки" => 'deliveryDate',
            "Время доствки с" => 'deliveryFrom',
            "Время доставки до" => 'deliveryTill',
            "Товар" => 'name',
            "Артикул" => 'sku',
            "Количество" => 'count',
            "Тип оплаты" => 'paymentType',
            "Стоимость ед. товара" => 'cost',
//            "Цена со скидкой" => 'discount_cost',
            "Стоимость доставки" => 'deliveryCost',
            "НДС" => 'vat',
            "Сумма к получению" => 'leftToPay',
            "Вес" => 'weight',
            "Бренд" => 'brand',
            "ТНВЭД" => 'tnved',
            "Страна производитель" => 'countryCode',
            "Баркод" => 'barcode',
            "Объём" => 'volume',
            "Длина" => 'width',
            "Высота" => 'height',
            "Глубина" => 'depth',
        ];
    }

    private function fileObiTitlesToDbColumnsPrepare(): array
    {
        return [
            "Дата доставки" => 'deliveryDate',
            "Время доставки" => 'deliveryTime',
            "№ заявки на доставку" => 'orderNumber',
            "№ поручения ОБИ" => 'orderType',
            "Клиент" => 'clientName',
            "Телефон" => 'phone',
            "Адрес доставки" => 'deliveryAddress',
            "Вид доставки" => 'deliveryType',
            "Зона доставки" => 'deliveryZone',
            "Превышение зоны доставки, км" => 'overDeliveryZoneKm',
            "Вес заказа, кг" => 'orderWeight',
            "Вид подъёма" => 'liftType',
            "Этаж подъёма на лифте" => 'liftFloor',
            "Вес подъёма на лифте, кг" => 'liftWeightKg',
            "Этаж подъёма вручную" => 'handLiftFloor',
            "Вес подъёма вручную, кг" => 'handLiftWeightKg',
            "Расстояние переноса, м" => 'transferDistance',
            "Вес переноса, кг" => 'transferWeight',
            "Стоимость доставляемого товара, руб." => 'productsCost',
            "Стоимость услуг транспортировки, руб." => 'costOfTransportation',
            "Стоимость услуг подъёма, руб." => 'liftCost',
            "Стоимость услуг переноса, руб." => 'transferCost',
            "Общая стоимость услуг доставки, руб." => 'totalDeliveryCost',
            "Комментарий к заявке" => 'comment',
            "Состав заказа" => 'orderList'
        ];
    }
}
