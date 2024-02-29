<?php

namespace App\Infrastructure\Admin\Services\Tariff;

use App\Domain\DTO\Requests\Tariff\TariffRegionCategoriesPricesDTO;
use App\Infrastructure\Repositories\Admin\TariffCategoryRepository;
use App\Infrastructure\Repositories\Admin\TariffCategorySettingsRepository;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Models\TariffRegionZone;
use App\Infrastructure\Services\Delivery\Api\Api;

class TariffService
{
    protected TariffRepository $tariffRepo;
    protected RegionRepository $regionRepo;
    protected TariffCategoryRepository $categoryRepo;
    protected TariffCategorySettingsRepository $categorySettingsRepo;
    protected Api $deliveryServiceApi;

    public function __construct(TariffRepository $tariffRepo,
                                RegionRepository $regionRepo,
                                TariffCategoryRepository $categoryRepo,
                                TariffCategorySettingsRepository $categorySettingsRepo,
                                Api $deliveryServiceApi
    )
    {
        $this->tariffRepo = $tariffRepo;
        $this->regionRepo = $regionRepo;
        $this->categoryRepo = $categoryRepo;
        $this->categorySettingsRepo = $categorySettingsRepo;
        $this->deliveryServiceApi = $deliveryServiceApi;
    }

    public function create(array $data): void
    {
        $newTariff = $this->tariffRepo->create($data);
        $response = $this->deliveryServiceApi
            ->query('settings/calculation/courier-delivery-price-tariffs', $data);

        if ($response) {
            $this->tariffRepo->updateByFields($newTariff, ['delivery_service_tariff_id' => $response['created_id']]);
        }
    }

    public function addRegions(int $tariffId, array $regions): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);

        foreach ($regions as $regionData) {
            $region = $this->regionRepo->getById($regionData['id']);
            $this->tariffRepo->saveRegion($tariff, $region);
        }
    }

    public function deleteRegion(int $tariffId, int $regionId): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);
        $this->tariffRepo->deleteRegion($tariff, $regionId);
    }

    // TODO отрефакторить структуру. Сделал  пока так из-за того, что поджимали сроки.
    public function prepareForVue(int $tariffId, int $regionId): array
    {
        $region = $this->regionRepo->findByIdWithRelationships($regionId, ['tariffCategories']);
        $zones = TariffRegionZone::all();
        $data = [];
        $enabledZone = [];
        $prices = [];

        foreach ($zones as $zone) {
            $data['zones'][$zone->id] = [
                'id' => $zone->id,
                'name' => $zone->code,
                'enabled' => false
            ];
            $data['categories'] = [];

            foreach ($region->tariffCategories as $category) {
                $priceEntity = $this->categoryRepo->getPrices($category, $tariffId, $regionId, $zone->id);

                if ($priceEntity) {
                    if (!in_array($zone->id, $enabledZone)) {
                        $data['zones'][$zone->id]['enabled'] = true;
                    }

                    $prices[$category->id][$zone->code] = [
                        'price' => $priceEntity->price,
                        'secondPrice' => $priceEntity->second_price,
                        'zone' => $priceEntity->zone_id
                    ];
                }

                if (!in_array($category->id . '_' . $zone->id, $data['categories'])) {
                    $settings = $this->categorySettingsRepo->get($tariffId, $regionId, $category->id);

                    $data['categories'][$category->id . '_' . $zone->id] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'isUse' => $settings ? (bool) $settings->is_use : false,
                        'prices' => $prices[$category->id] ?? []
                    ];
                }
            }
        }

        // сбрасываем индексы, чтобы vue воспринимал значения как массив, а не как объект (костыль)
        $data['categories'] = array_values($data['categories']);
        $data['zones'] = array_values($data['zones']);

        return $data;
    }

    public function saveRegionCategoriesPrices(int $tariffId, int $regionId, TariffRegionCategoriesPricesDTO $dto): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);
        $region = $this->regionRepo->getById($regionId);

        foreach ($dto->categories as $category) {
            $categoryEntity = $this->categoryRepo->getById($category->categoryId);
            $setting = $this->categorySettingsRepo->get($tariffId, $regionId, $category->categoryId);

            if (!$setting) {
                $this->categorySettingsRepo->create($tariffId, $regionId, $category->categoryId, $category->isUse);
            } else {
                $this->categorySettingsRepo->update($tariffId, $regionId, $category->categoryId, ['is_use' => $category->isUse]);
            }

            foreach ($category->prices as $priceInfo) {
                $price = $this->categoryRepo->getPrices($categoryEntity, $tariffId, $regionId, $priceInfo->zoneId);

                if ($price) {
                    $price->update(['price' => $priceInfo->price, 'second_price' => $priceInfo->secondPrice]);
                } else {
                    $price = $categoryEntity->prices()->create([
                        'tariff_id' => $tariffId,
                        'region_id' => $regionId,
                        'zone_id' => $priceInfo->zoneId,
                        'price' => $priceInfo->price,
                        'second_price' => $priceInfo->secondPrice
                    ]);
                }

                $result = $this->deliveryServiceApi->query('settings/calculation/courier-delivery-prices', [
                    'tariff_id' => $tariff->delivery_service_tariff_id,
                    'region_id' => $region->region_id,
                    'zone' => $priceInfo->zoneName,
                    'product_delivery_category_id' => $categoryEntity->category_id,
                    'price' => $price->price,
                    'price_second' => $price->second_price
                ]);

                if (!$result) {
                    throw new \Exception('Не удалось обновить цены в сервисе');
                }
            }
        }
    }

    public function deleteZone(int $tariffId, int $regionId, array $zoneData): void
    {
        foreach ($zoneData['categories'] as $category) {
            $category = $this->categoryRepo->getById($category);
            $this->categoryRepo->deletePriceByZoneIdAndRegionId($category, $tariffId, $regionId, $zoneData['zoneId']);
        }
    }

    public function deleteTariff(int $tariffId): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);

        if ($tariff->regions) {
            foreach ($tariff->regions as $region) {
                if ($region->tariffCategories) {
                    foreach ($region->tariffCategories as $category) {
                        foreach (TariffRegionZone::all() as $zone) {
                            $price = $this->categoryRepo->getPrices($category, $tariffId, $region->id, $zone->id);
                            $price?->delete();
                        }
                    }
                }
            }
        }
        $this->tariffRepo->delete($tariff);
    }

    public function cloneTariff(int $tariffId): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);
        $lastTariffId = $this->tariffRepo->getLastId();
        $clone = $tariff->replicate();
        $clone->alias = $tariff->alias . 'Clone_' . $lastTariffId;
        $clone->save();

        foreach ($tariff->regions as $region) {
            $clone->regions()->attach($region);

            foreach ($clone->regions as $cloneRegion) {
                foreach ($region->tariffCategories as $category) {
                    $cloneRegion->tariffCategories()->attach($category);
                }
            }
        }
    }
}