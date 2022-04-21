<?php

namespace App\Application;

use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\ProductRepository;

class ApplicationService
{
    protected $appRepo;
    protected $productRepo;

    public function __construct(ApplicationRepository $appRepo, ProductRepository $productRepo)
    {
        $this->appRepo = $appRepo;
        $this->productRepo = $productRepo;
    }

    public function checkAppChanges($app, array $data)
    {
        $differentValues = [];
        $modelProperties = $app->toArray();
        unset($modelProperties['id']);
        unset($modelProperties['status']);
        unset($modelProperties['created_at']);
        unset($modelProperties['updated_at']);
        unset($modelProperties['doc_ver']);
        $data['delivery_time'] = $data['delivery_from'] . '-' . $data['delivery_till'];

        if ($modelProperties != $data) {
            $data['doc_ver'] = $app->doc_ver + 1;
            $app->update($data);
        }

        return null;
    }

    public function checkAppProducts($app, array $data)
    {
        foreach ($data as $csvProduct) {
            $appProduct = $this->productRepo->getByAppIdSkuBrand($csvProduct['brand'], $csvProduct['sku'], $app->id);

            if (!$appProduct) {
                $app->update(['doc_ver' => $app->doc_ver + 1]);
                $this->productRepo->create($csvProduct);
            } else {
                $csvProduct['app_id'] = $app->id;
                $this->productRepo->update($csvProduct, $appProduct);
            }
        }
    }
}
