<?php

namespace App\Infrastructure\Admin\Services\Tariff;

use App\Domain\DTO\Requests\Tariff\TariffRegionCategoriesPricesDTO;
use App\Infrastructure\Repositories\Admin\TariffCategoryRepository;
use App\Infrastructure\Repositories\Admin\TariffCategorySettingsRepository;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\Admin\UserTariffPermissionRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Models\TariffCategoryPrices;
use App\Models\TariffRegionZone;
use App\Infrastructure\Services\Delivery\Api\Api;
use App\Infrastructure\Repositories\Admin\ZoneRepository;
use Illuminate\Support\Facades\Log;
use Exception;

class TariffService
{
    protected TariffRepository $tariffRepo;
    protected RegionRepository $regionRepo;
    protected TariffCategoryRepository $categoryRepo;
    protected TariffCategorySettingsRepository $categorySettingsRepo;
    protected Api $deliveryServiceApi;
    protected ZoneRepository $zoneRepo;
    protected UserTariffPermissionRepository $permissionRepo;

    public function __construct(TariffRepository $tariffRepo,
                                RegionRepository $regionRepo,
                                TariffCategoryRepository $categoryRepo,
                                TariffCategorySettingsRepository $categorySettingsRepo,
                                Api $deliveryServiceApi,
                                ZoneRepository $zoneRepo,
                                UserTariffPermissionRepository $permissionRepo
    )
    {
        $this->tariffRepo = $tariffRepo;
        $this->regionRepo = $regionRepo;
        $this->categoryRepo = $categoryRepo;
        $this->categorySettingsRepo = $categorySettingsRepo;
        $this->deliveryServiceApi = $deliveryServiceApi;
        $this->zoneRepo = $zoneRepo;
        $this->permissionRepo = $permissionRepo;
    }

    /**
     * @throws Exception
     */
    public function create(array $data): void
    {
        $newTariff = $this->tariffRepo->create($data);
        $token = $this->getServiceToken();
        $response = $this->deliveryServiceApi
                ->query('settings/calculation/courier-delivery-price-tariffs', $data, 'POST', $token);
        Log::info('response ' . json_encode($response));

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
        $token = $this->getServiceToken();

        foreach ($dto->categories as $category) {
            $categoryEntity = $this->categoryRepo->getById($category->categoryId);
            $setting = $this->categorySettingsRepo->get($tariffId, $regionId, $category->categoryId);

            if (!$setting) {
                $this->categorySettingsRepo->create($tariffId, $regionId, $category->categoryId, $category->isUse);
            } else {
                $this->categorySettingsRepo->update($tariffId, $regionId, $category->categoryId, ['is_use' => $category->isUse]);
            }

            $priceForUpdate = [];

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

            $result = $this->deliveryServiceApi
                ->query('settings/calculation/group/courier-delivery-prices', $priceForUpdate, 'PUT', $token);

            if (!$result) {
                throw new Exception('Не удалось обновить цены в сервисе');
            }
        }
    }

    /**
     * @throws Exception
     */
    public function deleteZone(int $tariffId, int $regionId, array $zoneData): void
    {
        $token = $this->getServiceToken();
        $tariff = $this->tariffRepo->findById($tariffId);

        foreach ($zoneData['categories'] as $categoryInfo) {
            $category = $this->categoryRepo->getById($categoryInfo['id']);
            $result = $this->deliveryServiceApi
                    ->query('settings/calculation/group/courier-delivery-prices', [
                        'region_id' => $regionId,
                        'tariff_id' => $tariff->delivery_service_tariff_id,
                        'zone' => $zoneData['zone'],
                        'product_delivery_category_id' => $category->result_category_id,
                    ], 'DELETE', $token);

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

        if ($tariff->regions) {
            foreach ($tariff->regions as $region) {
                if ($region->tariffCategories) {
                    foreach ($region->tariffCategories as $category) {
                        foreach (TariffRegionZone::all() as $zone) {
                            $price = $this->categoryRepo->getPrices($category, $tariffId, $region->id, $zone->id);
                            $price?->delete();
                        }

                        $this->categorySettingsRepo->delete($tariffId, $region->id, $category->id);
                    }
                }
            }
        }

        $this->permissionRepo->deleteByTariffId($tariffId);
        $this->tariffRepo->delete($tariff);
        $token = $this->getServiceToken();
        $result = $this->deliveryServiceApi
            ->query('settings/calculation/courier-delivery-price-tariffs/' . $tariff->delivery_service_tariff_id, [], 'DELETE', $token);

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
        $token = $this->getServiceToken();
        $clone->save();

        $response = $this->deliveryServiceApi
            ->query('settings/calculation/courier-delivery-price-tariffs', [
                'name' => $clone->name,
                'alias' => $clone->alias
            ], 'POST', $token);

        if ($response) {
            $this->tariffRepo->updateByFields($clone, ['delivery_service_tariff_id' => $response['created_id']]);
        }

        if ($tariff->regions) {
            foreach ($tariff->regions as $region) {
                $clone->regions()->attach($region);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function getFromDeliveryService(): void
    {
        $tariffsFromService = $this->deliveryServiceApi
            ->query('settings/calculation/courier-delivery-price-tariffs', [], 'GET');
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

            foreach ($regions as $region) {
                $this->tariffRepo->saveRegion($localTariff, $region);
                $pricesFromService = $this->deliveryServiceApi
                    ->query('settings/calculation/group/courier-delivery-prices',
                        [
                            'region_id' => $region->region_id,
                            'tariff_id' => $localTariff->delivery_service_tariff_id
                        ], 'GET');

                foreach ($pricesFromService as $priceFromService) {
                    $zone = $this->zoneRepo->getByCode($priceFromService['zone']);
                    $category = $this->categoryRepo
                        ->getByProductCategoryId($priceFromService['product_delivery_category_id']);
                    $price = $this->categoryRepo
                            ->getPrices($category, $localTariff->id, $region->id, $zone->id, $priceFromService['id']);

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
    }

    /**
     * @throws Exception
     */
    private function getServiceToken(): string
    {
        $authResponse = $this->deliveryServiceApi->query('auth/login', [
            'email' => config('services.delivery_holodilnik_service.login'),
            'password' => config('services.delivery_holodilnik_service.password')
        ]);

        if ($authResponse && isset($authResponse['access_token'])) {
            return $authResponse['access_token'];
        } else {
            throw new Exception('Не удалось авторизоваться в сервисе Delivery');
        }
    }
}