<?php

namespace App\Infrastructure\Services\Application;

use App\Domain\DTO\ApplicationDTO;
use App\Domain\DTO\ProductDTO;
use App\Domain\Enum\ApplicationStatus;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Models\Application;

class ApplicationCheckService
{
    public function __construct(private readonly ApplicationRepository $appRepo,
                                private readonly ProductRepository $productRepo
    )
    {}

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