<?php

namespace App\Infrastructure\Admin\Services\Tariff;

use App\Domain\DTO\Requests\Tariff\TariffRegionCategoriesPricesDTO;
use App\Infrastructure\Repositories\Admin\TariffCategoryRepository;
use App\Infrastructure\Repositories\Admin\TariffCategorySettingsRepository;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\Admin\UserTariffPermissionRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Models\TariffRegionZone;
use App\Infrastructure\Repositories\Admin\ZoneRepository;
use Exception;
use App\Infrastructure\Services\HruGateway\Api as HruGatewayApi;

class TariffService
{
    private TariffRepository $tariffRepo;
    private RegionRepository $regionRepo;
    private TariffCategoryRepository $categoryRepo;
    private TariffCategorySettingsRepository $categorySettingsRepo;
    private ZoneRepository $zoneRepo;
    private UserTariffPermissionRepository $permissionRepo;
    protected string $token;

    private HruGatewayApi $hruGatewayApi;

    public function __construct(TariffRepository $tariffRepo,
                                RegionRepository $regionRepo,
                                TariffCategoryRepository $categoryRepo,
                                TariffCategorySettingsRepository $categorySettingsRepo,
                                ZoneRepository $zoneRepo,
                                UserTariffPermissionRepository $permissionRepo,
                                HruGatewayApi $hruGatewayApi
    )
    {
        $this->tariffRepo = $tariffRepo;
        $this->regionRepo = $regionRepo;
        $this->categoryRepo = $categoryRepo;
        $this->categorySettingsRepo = $categorySettingsRepo;
        $this->zoneRepo = $zoneRepo;
        $this->permissionRepo = $permissionRepo;
        $this->hruGatewayApi = $hruGatewayApi;
        $this->token = config('services.hru_gateway.api_delivery_service_token');
    }

    /**
     * @throws Exception
     */
    public function create(array $data): void
    {
        $newTariff = $this->tariffRepo->create($data);
        $response = $this->hruGatewayApi
            ->tariffs('settings/calculation/courier-delivery-price-tariffs', $data, $this->token);

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

    public function addAllRegions(int $tariffId): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);
        $regions = $this->regionRepo->getAll();
        $this->tariffRepo->addAllRegions($tariff, $regions);
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
        $tariff = $this->tariffRepo->findById($tariffId);
        $zones = TariffRegionZone::all();
        $data = [];
        $enabledZone = [];
        $prices = [];
        $data['authorId'] = $tariff->author_id;

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

    /**
     * @throws Exception
     */
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

            $priceForUpdate = [];

            if ($category->prices) {
                foreach ($category->prices as $priceInfo) {
                    $price = $this->categoryRepo->getPrices($categoryEntity, $tariffId, $regionId, $priceInfo->zoneId);

                    if ($price) {
                        if ($price->price != $priceInfo->price || $price->second_price != $priceInfo->secondPrice) {
                            $price->update(['price' => $priceInfo->price, 'second_price' => $priceInfo->secondPrice]);
                        }
                    } else {
                        $categoryEntity->prices()->create([
                            'tariff_id' => $tariffId,
                            'region_id' => $regionId,
                            'zone_id' => $priceInfo->zoneId,
                            'price' => $priceInfo->price,
                            'second_price' => $priceInfo->secondPrice
                        ]);
                    }

                    $priceForUpdate['items'][] = [
                        'region_id' => $region->region_id,
                        'tariff_id' => $tariff->delivery_service_tariff_id,
                        'zone' => $priceInfo->zoneName,
                        'product_delivery_category_id' => $categoryEntity->result_category_id,
                        'price' => $priceInfo->price,
                        'price_second' => $priceInfo->secondPrice
                    ];
                }

                $result = $this->hruGatewayApi
                    ->tariffs('settings/calculation/group/courier-delivery-prices', $priceForUpdate,  $this->token, 'PUT');

                if (!$result) {
                    throw new Exception('Не удалось обновить цены в сервисе');
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function deleteZone(int $tariffId, int $regionId, array $zoneData): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);

        foreach ($zoneData['categories'] as $categoryInfo) {
            $category = $this->categoryRepo->getById($categoryInfo['id']);
            $result = $this->hruGatewayApi
                ->tariffs('settings/calculation/group/courier-delivery-prices', [
                    'region_id' => $regionId,
                    'tariff_id' => $tariff->delivery_service_tariff_id,
                    'zone' => $zoneData['zone'],
                    'product_delivery_category_id' => $category->result_category_id,
                ], $this->token, 'DELETE');

            if (!$result) {
                throw new Exception('Не удалось удалить цены в сервисе');
            }

            $this->categoryRepo->deletePriceByZoneIdAndRegionId($category, $tariffId, $regionId, $zoneData['zoneId']);
        }
    }

    /**
     * @throws Exception
     */
    public function deleteTariff(int $tariffId): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);
        $this->permissionRepo->deleteByTariffId($tariffId);
        $this->tariffRepo->delete($tariff);
        $result = $this->hruGatewayApi
            ->tariffs('settings/calculation/courier-delivery-price-tariffs/' . $tariff->delivery_service_tariff_id, [],
                $this->token, 'DELETE');

        if (!$result) {
            throw new Exception('Не удалось удалить тариф в сервисе');
        }
    }

    /**
     * @throws Exception
     */
    public function cloneTariff(int $tariffId): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);
        $clone = $tariff->replicate();
        $clone->alias = $tariff->alias . 'Clone_' . substr(md5(mt_rand()), 0, 7);;
        $clone->save();

        $response = $this->hruGatewayApi
            ->tariffs('settings/calculation/courier-delivery-price-tariffs', [
                'name' => $clone->name,
                'alias' => $clone->alias
            ], $this->token);

        if ($response) {
            $this->tariffRepo->updateByFields($clone, ['delivery_service_tariff_id' => $response['created_id']]);
        }

        if ($tariff->regions) {
            foreach ($tariff->regions as $region) {
                $clone->regions()->attach($region);
                $categories = $region->tariffCategories;

                foreach ($categories as $category) {
                    $prices = $category->prices()->where('region_id', $region->id)
                        ->where('tariff_id', $tariff->id)->get();

                    if ($prices) {
                        foreach ($prices as $price) {
                            $category->prices()->create([
                                'tariff_id' => $clone->id,
                                'region_id' => $region->id,
                                'zone_id' => $price->zone_id,
                                'price' => $price->price,
                                'second_price' => $price->second_price
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function getFromDeliveryService(): bool
    {
        $tariffsFromService = $this->hruGatewayApi
            ->tariffs('settings/calculation/courier-delivery-price-tariffs', [], null, 'GET');
        $regions = $this->regionRepo->getAll();

        foreach ($tariffsFromService as $tariffFromService) {
            $localTariff = $this->tariffRepo->findByTariffServiceId($tariffFromService['id']);

            if (!$localTariff) {
                $localTariff = $this->tariffRepo->findByTariffServiceAlias($tariffFromService['alias']);

                if (!$localTariff) {
                    $localTariff = $this->tariffRepo->create([
                        'name' => $tariffFromService['name'],
                        'alias' => $tariffFromService['alias'],
                        'delivery_service_tariff_id' => $tariffFromService['id'],
                        'author_id' => 0
                    ]);
                }
            }

            if (count($localTariff->regions) > 0) {
                $regions = $localTariff->regions;
            }

            foreach ($regions as $region) {
                $this->tariffRepo->saveRegion($localTariff, $region);
                $pricesFromService = $this->hruGatewayApi
                    ->tariffs('settings/calculation/group/courier-delivery-prices',
                        [
                            'region_id' => $region->region_id,
                            'tariff_id' => $localTariff->delivery_service_tariff_id
                        ], null, 'GET');

                foreach ($pricesFromService as $priceFromService) {
                    $zone = $this->zoneRepo->getByCode($priceFromService['zone']);
                    $category = $this->categoryRepo
                        ->getByProductCategoryId($priceFromService['product_delivery_category_id']);
                    $price = $this->categoryRepo
                            ->getPrices($category, $localTariff->id, $region->id, $zone->id);

                    if (!$price) {
                        $category->prices()->create([
                            'tariff_id' => $localTariff->id,
                            'region_id' => $region->id,
                            'zone_id' => $zone->id,
                            'price' => $priceFromService['price'],
                            'second_price' => $priceFromService['price_second']
                        ]);
                    }
                }
            }
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function updateNameOrAlias(int $tariffId, array $data): void
    {
        $tariff = $this->tariffRepo->findById($tariffId);
        $this->tariffRepo->updateByFields($tariff, $data);
        $this->hruGatewayApi
            ->tariffs('settings/calculation/courier-delivery-price-tariffs/' . $tariff->delivery_service_tariff_id,
                $data, $this->token, 'PATCH');

    }
}
