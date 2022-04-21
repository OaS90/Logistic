<?php

namespace App\Application;

use App\Domain\ApplicationDTO;
use App\Domain\DeliveryAddressDTO;
use App\Domain\ProductDTO;
use Maatwebsite\Excel\Facades\Excel;
use App\Infrastructure\Imports\ImportEntity;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\ProductRepository;

/**
 *
 */
class CsvImportService
{
    protected $appRepo;
    protected $productRepo;
    protected $addressRepo;

    public function __construct(ApplicationRepository $appRepo,
                                DeliveryAddressRepository $addressRepository,
                                ProductRepository $productRepository
    )
    {
        $this->appRepo = $appRepo;
        $this->productRepo = $productRepository;
        $this->addressRepo = $addressRepository;
    }


    public function import($file, ImportEntity $entity, $userId)
    {
        $dataFromCsv = $this->getDataArraysWithDbRows($entity, $file);

        foreach ($dataFromCsv as $data) {
            $address = $this->addressRepo->createFromCsv($data['address']);
            $data['app']['delivery_address'] = $address->id;
            $data['app']['user_id'] = $userId;
            $data['app']['delivery_time'] = $data['app']['delivery_from'] . '-' . $data['app']['delivery_till'];
            $app = $this->appRepo->create($data['app']);

            foreach ($data['products'] as $dataProduct) {
                $dataProduct['app_id'] = $app->id;
                $this->productRepo->create($dataProduct);
            }

        }
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
            $appWithDbColumns = array_combine($rows, $dataFromFile[$i]);

            if ($appWithDbColumns['order_number'] == null) {
                $apps[$i - 1]['products'][] = (new ProductDTO())->dbRows($appWithDbColumns);
                continue;
            }

            $apps[$i]['app'] = (new ApplicationDTO())->dbRows($appWithDbColumns);
            $apps[$i]['address'] = (new DeliveryAddressDTO())->dbRows($appWithDbColumns);
            $apps[$i]['products'][] = (new ProductDTO())->dbRows($appWithDbColumns);

        }

        return $apps;
    }
}
