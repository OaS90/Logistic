<?php

namespace App\Infrastructure\Services\YandexZones;

use App\Http\Controllers\Api\Exceptions\DeletedZoneImportException;
use App\Http\Controllers\Api\Exceptions\NewZoneImportException;
use App\Infrastructure\Events\EventDispatcher;
use App\Infrastructure\Repositories\Admin\RegionRepository;
use App\Infrastructure\Repositories\Admin\TariffCategoryRepository;
use App\Infrastructure\Repositories\Admin\TariffRepository;
use App\Infrastructure\Repositories\Admin\ZoneRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Services\Kraken\Api;
use App\Models\Region;
use Illuminate\Support\Facades\Storage;

class YandexZonesService
{
    const FILE_NAME = 'zones.json';
    const NEW_ZONES_FILE_NAME = 'new.json';
    const UPDATED_NEW_ZONES_FILE_NAME = 'updated_new.json';
    const UPDATED_FILE_NAME = 'updated_zones.json';
    public function __construct(private readonly Api $krakenApi,
                                private readonly FilialRepository $filialRepo,
                                private readonly TariffRepository $tariffRepo,
                                private readonly TariffCategoryRepository $tariffCategoryRepo,
                                private readonly RegionRepository $regionRepo,
                                private readonly ZoneRepository $zoneRepo,
                                private readonly EventDispatcher $eventDispatcher
    ) {}

    /**
     * Метод получения данных по полигонам
     * из сервиса holodilnik-delivery
     */
    public function exportFromDelivery(): array
    {
        $allRegions = Region::all();
        $polygonsCoordinates = [];

        foreach ($allRegions as $region) {
            $polygons = $this->krakenApi
                ->deliveryServiceRequest('settings/delivery-zones/find-all-by-hru-region-id', ['region_id' => $region->region_id]);
            $polygonsCoordinatesByRegion = $this->prepareCoordinates($polygons);
            $polygonsCoordinates = array_merge($polygonsCoordinatesByRegion, $polygonsCoordinates);
        }

        $allowZones = $this->krakenApi->deliveryServiceRequest('settings/allow-zones');
        $allowZonesCoordinates = $this->prepareCoordinates($allowZones, 'allow-zones');
        $polygonPaths = $this->krakenApi->deliveryServiceRequest('settings/polygon-paths');
        $polygonPathsCoordinates = $this->prepareCoordinates($polygonPaths, 'polygon-paths');
        $polygons = array_merge($polygonsCoordinates, $allowZonesCoordinates, $polygonPathsCoordinates);
        $polygonsData = $this->prepareDataForImportToFile($polygons);
        $jsonData = json_encode($polygonsData);

        if (Storage::exists(self::FILE_NAME)) {
            Storage::delete(self::FILE_NAME);
        }

        Storage::put(self::FILE_NAME, $jsonData);

        return $polygonsData;
    }

    /**
     * @throws \Exception
     */
    public function handleBeforeImportToService(array $newData): array
    {
        if (Storage::exists(self::FILE_NAME)) {
            $contents = Storage::get(self::FILE_NAME);
            $oldData = json_decode($contents, true);
            $changes = [];
            $notFoundPolygons = [];
            $diffsRegions = [];
            $newPolygons = [];
            $oldDataDescriptions = [];
            $newDataDescriptions = [];

            foreach ($newData['features'] as $newPolygon) {
                $newCoordinates = $newPolygon['geometry']['coordinates'][0];
                $newDescription = trim($newPolygon['properties']['description']);
                $newExplodedDescription = explode('|', $newDescription);

                // Проверяем, что описании соответствует структуре
                // Если нет, то это новый полигон, который нужно обработать
                // и вывести для него нужные настроки
                if (count($newExplodedDescription) == 1) {
                    $newPolygons[] = $newPolygon;
                    continue;
                }

                // записываем описание полигонов из яндекс карт
                $newDataDescriptions[trim($newPolygon['properties']['description'])] = trim($newPolygon['properties']['description']);

                foreach ($oldData['features'] as $oldIndex => $polygon) {
                    $oldCoordinates = $polygon['geometry']['coordinates'][0];
                    $explodedDescription = explode('|', $polygon['properties']['description']);

                    // записываем описание полигонов из яндекс карт
                    if (!isset($oldDataDescriptions[$polygon['properties']['description']])) {
                        $oldDataDescriptions[$polygon['properties']['description']] = $oldIndex;
                    }

                    if ($polygon['properties']['description'] == $newDescription) {
                        $diffs = $this->hasChanges($oldCoordinates, $newCoordinates);

                        if ($diffs) {
                            $diffsRegions[$explodedDescription[0]] = $explodedDescription;
                            $changes[$polygon['properties']['description']] = $this->makePolygonData($polygon, $explodedDescription, $newCoordinates);
                        }
                    } else {
                        if (!isset($explodedDescription[1])) {
                            $notFoundPolygons[$explodedDescription[1]] = ['id' => $newPolygon['id']];
                        }
                    }
                }
            }

            // Проверяем на отсутствие какого-либо описания (удалённая зона)
            // и выводим её
            $deletedPolygonsKeys = array_diff_key($oldDataDescriptions, $newDataDescriptions);
            $deletedPolygons = [];

            foreach ($deletedPolygonsKeys as $description => $oldKey) {
                $oldPolygon = $oldData['features'][$oldKey];
                $explodedDescription = explode('|', $description);
                $deletedPolygons[] = $this->makePolygonData($oldPolygon, $explodedDescription);
            }

            Storage::put(self::UPDATED_FILE_NAME, json_encode($newData));

            if ($newPolygons) {
                Storage::put(self::NEW_ZONES_FILE_NAME, json_encode($newPolygons));
            }
        } else {
            throw new \Exception('Не найден файл с настройками из сервиса');
        }

        return [
            'changes' => array_values($changes),
            'notFoundPolygons' => $notFoundPolygons,
            'diffsRegions' => $diffsRegions,
            'newPolygons' => $newPolygons,
            'deletedPolygons' => $deletedPolygons
        ];
    }

    /**
     * Метод импорта изменённых зон в сервис holodilnik-delivery
     * Зоны, которые нужно обновить (включает удалённые, новые и изменённые, которые были выбраны)
     * @param array $zonesToUpdate
     * =====================================
     * Все зоны
     * @param array $allZones
     * @throws NewZoneImportException|DeletedZoneImportException
     */
    public function importToService(array $zonesToUpdate, array $allZones): void
    {
        if ($zonesToUpdate['changed']) {
            $this->importZonesForChange($zonesToUpdate['changed'], $allZones['changed']);
        }

        if ($zonesToUpdate['new']) {
            $this->importNewZones($zonesToUpdate['new']);
        }

        if ($zonesToUpdate['deleted']) {
            $this->importZonesForDelete($zonesToUpdate['deleted']);
        }
        //отпрака в кафку. Пока отложено.
        //  $this->eventDispatcher->filialZoneChanged($codes);
    }

    private function importZonesForChange(array $zonesToImport, array $allZones): void
    {
        // берём зоны, которые не отметили для отправки в сервис
        $filialIdsToUpdate = array_column($zonesToImport['changed'], 'filial_id');
        $filialIdsChangedZones = array_column($allZones['changed'], 'filial_id');
        $notSelectedZoneCodes = array_diff($filialIdsChangedZones, $filialIdsToUpdate);
        $notSelectedZones = array_filter($allZones['changed'], function ($zone) use ($notSelectedZoneCodes) {
            return in_array($zone['filial_id'], $notSelectedZoneCodes);
        });

        // Сначала изменяем координаты для всех зон
        foreach ($zonesToImport as &$zone) {
            foreach ($zone['points'] as &$point) {
                list($point[0], $point[1]) = [$point[1], $point[0]];
            }
        }

        $deliveryZones = array_filter($zonesToImport, function ($zone) {
            if ($zone['type'] == 'delivery-zones') {
                return true;
            }

            return false;
        });

        $allowZones = array_filter($zonesToImport, function ($zone) {
            if ($zone['type'] == 'allow_zones') {
                return true;
            }

            return false;
        });

        if ($deliveryZones) {
            foreach ($deliveryZones as $deliveryZone) {
                $deliveryZone['region_id'] = (int) $deliveryZone['region_id'];
                $this->krakenApi->deliveryServiceRequest('settings/delivery-zones/', $deliveryZone, 'PATCH');
            }
        }

        if ($allowZones) {
            foreach ($allowZones as $allowZone) {
                $allowZone['region_id'] = (int) $allowZone['region_id'];
                $this->krakenApi->deliveryServiceRequest('settings/allow-zones', $allowZone, 'PATCH');
            }
        }

        if ($notSelectedZones) {
            $notSelectedAllowZones = array_filter($allowZones, function ($zone) use ($notSelectedZoneCodes) {
                if (in_array($zone['filial_id'], $notSelectedZoneCodes) && $zone['type'] == 'allow_zone') {
                    return true;
                }

                return false;
            });

            $notSelectedDeliveryZones = array_filter($allowZones, function ($zone) use ($notSelectedZoneCodes) {
                if (in_array($zone['filial_id'], $notSelectedZoneCodes) && $zone['type'] == 'polygon') {
                    return true;
                }

                return false;
            });

            $preparedNotSelectedAllowZones = $this->prepareCoordinates($notSelectedAllowZones, 'allow-zones');
            $preparedNotSelectedDeliveryZones = $this->prepareCoordinates($notSelectedDeliveryZones);
            $preparedNotSelectedZones = array_merge($preparedNotSelectedDeliveryZones, $preparedNotSelectedAllowZones);
            $jsonData = json_encode($this->prepareDataForImportToFile($preparedNotSelectedZones));

            // Если есть файл, который выгружали из сервиса вначале, то удаляем его
            if (Storage::exists(self::FILE_NAME)) {
                Storage::delete(self::FILE_NAME);
            }

            // Если есть файл, с обновлёнными данными (с изменениями из яндекс карт), то его тоже удаляем
            if (Storage::exists(self::UPDATED_FILE_NAME)) {
                Storage::delete(self::UPDATED_FILE_NAME);
            }

            // И кладём предыдущую выгрузку из сервиса для того,
            Storage::put(self::FILE_NAME, $jsonData);
        } else {
            Storage::delete(self::FILE_NAME);
            Storage::move(self::UPDATED_FILE_NAME, self::FILE_NAME);
        }
    }

    /**
     * @throws NewZoneImportException
     */
    private function importNewZones(array $zones): void
    {
        foreach ($zones as $zone) {
            $coordinates = [];

            foreach ($zone['polygon_data']['geometry']['coordinates'][0] as &$point) {
                list($point[0], $point[1]) = [$point[1], $point[0]];
                $coordinates[] = $point;
            }

            $newZoneDataForService = [
                'region_id' => $zone['region']['region_id'],
                'filial_id' => $zone['filial']['filial_id'],
                'points' => $coordinates,
                'zone' => $zone['zone']
            ];

            if ($zone['type'] == 'delivery-zones') {
                $tariffs = $this->tariffRepo->getAll();
                $categories = $this->tariffCategoryRepo->getAll();
                $regions = $this->regionRepo->getAll();
                $zone = $this->zoneRepo->getByCode($zone['zone']);

                foreach ($tariffs as $tariff) {
                    foreach ($regions as $region) {
                        foreach ($categories as $category) {
                            $this->tariffCategoryRepo
                                ->createPrice($category, $tariff->id, $region->id, $zone->id);
                        }
                    }
                }
            }

            $creatingResponse = $this->krakenApi
                ->deliveryServiceRequest('settings/' . $zone['type'], $newZoneDataForService, 'POST');

            if (!$creatingResponse) {
                throw new NewZoneImportException($newZoneDataForService);
            }
        }
    }

    /**
     * @throws DeletedZoneImportException
     */
    private function importZonesForDelete(array $zonesToImport): void
    {
        foreach ($zonesToImport as $zone) {
            $successDelete = $this->krakenApi
                    ->deliveryServiceRequest('settings/' . $zone['type'] . '/' . $zone['id'], [],'DELETE');

            if (!$successDelete) {
                throw new DeletedZoneImportException($zone);
            }

            if ($successDelete['status'] == 'success') {
                if ($zone['type'] == 'delivery-zones') {
                    $tariffs = $this->tariffRepo->getAll();
                    $categories = $this->tariffCategoryRepo->getAll();
                    $regions = $this->regionRepo->getAll();
                    $zone = $this->zoneRepo->getByCode($zone['zone']);

                    foreach ($tariffs as $tariff) {
                        foreach ($regions as $region) {
                            foreach ($categories as $category) {
                                $this->tariffCategoryRepo
                                    ->deletePriceByZoneIdAndRegionId($category, $tariff->id, $region->id, $zone->id);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     *
     * Метод создаёт структуру для яндекс карты, для каждого полигона
     * - описание
     * - заливка
     * - прозрачность
     * - обводка
     * Также меням местами координаты долготы и широты
     * т.к. в картах яндекса это требуеется
     *
     */
    private function prepareCoordinates(array $polygons, string $type = 'delivery-zones'): array
    {
        $data = [];

        foreach ($polygons as $polygon) {
            $regionId = isset($polygon['region']) ? $polygon['region']['id'] : $polygon['region_id'];
            $region = Region::where('region_id', $regionId)->first();
            $name = '';
            $fillOpacity = 0.6;
            $stroke = '82cdff';
            $strokeOpacity = 0.9;
            $regionId = '';
            $filialCode = null;

            if (isset($polygon['filial_id'])) {
                $filial = $this->filialRepo->getByFilialId($polygon['filial_id']);

                if ($filial) {
                    $filialCode = $filial->filial_id;
                } else {
                    $filialCode = $polygon['filial_id'];
                }
            }

            if ($region) {
                $name = $region->name;
                $regionId = $region->region_id;
            }

            if ($type === 'allow-zones') {
                $description = $regionId . '|allow-zones|' . $name . '|' . $filialCode;
                $fill = '#e6761b';
                $fillOpacity = 0.05;
                $stroke = '#ed4543';
                $strokeOpacity = 0.2;
            } elseif ($type === 'polygon-paths') {
                $description = $regionId . '|polygon-paths|' . $name;
                $fillOpacity = 0;
                $fill = '#dddddd';
                $stroke = '#dddddd';
                $strokeOpacity = 0.4;
            } else {
                if ($polygon['code_short'] == 'B') {
                    $fillOpacity = 0.4;
                    $fill = '#ff931e';
                } elseif ($polygon['code_short'] == 'C') {
                    $fillOpacity = 0.2;
                    $fill = '#e6761b';
                } else {
                    $fill = '#ffd21e';
                }

                $description = $regionId . '|delivery-zones|' . $polygon['code'] .  '|' . $name . '|' . $filialCode;
            }

            $uniquePoints = [];

            foreach ($polygon['points'] as $index => &$point) {
                list($point[0], $point[1]) = [$point[1], $point[0]];

                if (in_array($point, $uniquePoints, true)) {
                    unset($polygon['points'][$index]); // Удаляем дубликат
                } else {
                    $uniquePoints[] = $point; // Добавляем уникальную точку
                }
            }

            $polygon['points'] = array_values($uniquePoints);

            $data[] = [
                'type' => 'Feature',
                'id' => $polygon['id'],
                'properties' => [
                    'description' => trim($description),
                    'fill' => $fill,
                    'fill-opacity' => $fillOpacity,
                    'stroke' => $stroke,
                    'stroke-width' => '5',
                    'stroke-opacity' => $strokeOpacity
                ],
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [$polygon['points']]
                ]
            ];
        }

        return array_values($data);
    }

    // Функция для проверки наличия изменений
    private function hasChanges($oldCoordinates, $newCoordinates): bool|array
    {
        $diffs = [];
        if (count($oldCoordinates) !== count($newCoordinates)) {
            return true;
        }

        foreach ($newCoordinates as $index => $newCoord) {
            if ($oldCoordinates[$index][0] !== $newCoord[0] || $oldCoordinates[$index][1] !== $newCoord[1]) {
                $diffs[] = [
                    'index' => $index,
                    'old' => implode(', ', [
                        $oldCoordinates[$index][1],
                        $oldCoordinates[$index][0]
                    ]),
                    'new' => implode(', ',[
                        $newCoord[1],
                        $newCoord[0]
                    ])
                ];
            }
        }

        if ($diffs) {
            return $diffs;
        }

        return false; // Нет изменений
    }

    private function prepareDataForImportToFile(array $polygonsData): array
    {
        $data['type'] = 'FeatureCollection';
        $data['metadata'] = [
            'name' => 'maps',
            'creator' => 'BoF Holodilnik'
        ];
        $data['features'] = $polygonsData;

        return $data;
    }

    /**
     * Подгатавыливает стурктуру для vue и последующей
     * отправки в сервис
     */
    private function makePolygonData(array $polygon, array $description, array $newCoordinates = []): array
    {
        if ($description[1] == 'allow_zones') {
            $zoneName = '-';
            $regionName = $description[2];
            $filialId = $description[3];
            $zoneCodeShort = null;
            $zoneCode = null;
            $filialCode = sprintf('%05d', $filialId);
        } elseif ($description[1] == 'polygon-paths') {
            $zoneName = '-';
            $regionName = $description[2];
            $filialId = null;
            $filialCode = '-';
            $zoneCode = '-';
            $zoneCodeShort = '-';
        } else {
            $zoneName = $description[2];
            $regionName = $description[3];
            $filialId = $description[4];
            $zoneCode = $description[2];
            $zoneCodeShort = explode('_', $zoneCode)[1];
            $filialCode = sprintf('%05d', $filialId);
        }

        return [
            'id' => $polygon['id'],
            'zone_name' => $zoneName,
            'zone_code' => $description[3] ?? null,
            'region_name' => $regionName,
            'points' => $newCoordinates ?? $polygon['points'],
            'region_id' => $description[0],
            'type' => $description[1],
            'filial_code' => $filialCode,
            'filial_id' => (int) $filialId,
            'code_short' => $zoneCodeShort,
            'code' => $zoneCode,
            'to_import' => false
        ];
    }
}
