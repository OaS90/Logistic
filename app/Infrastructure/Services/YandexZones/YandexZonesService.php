<?php

namespace App\Infrastructure\Services\YandexZones;

use App\Infrastructure\Events\EventDispatcher;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Services\Kraken\Api;
use App\Models\Region;
use Exception;
use Illuminate\Support\Facades\Storage;

class YandexZonesService
{
    const FILE_NAME = 'zones.json';
    const UPDATED_FILE_NAME = 'updated_zones.json';
    public function __construct(private readonly Api $krakenApi,
                                private readonly FilialRepository $filialRepo,
                                private readonly EventDispatcher $eventDispatcher
    ) {}

    /**
     * @throws Exception
     */
    public function importFromDelivery(array $newData): array
    {
        if (Storage::exists(self::FILE_NAME)) {
            $contents = Storage::get(self::FILE_NAME);
            $oldData = json_decode($contents, true);
            $changes = [];
            $notFoundPolygons = [];
            $diffsRegions = [];

            foreach ($newData['features'] as $newPolygon) {
                $newCoordinates = $newPolygon['geometry']['coordinates'][0];
                $newDescription = trim($newPolygon['properties']['description']);

                foreach ($oldData['features'] as $polygon) {
                    $oldCoordinates = $polygon['geometry']['coordinates'][0];
                    $explodedDescription = explode('|', $polygon['properties']['description']);

                    if ($polygon['properties']['description'] == $newDescription) {
                        $diffs = $this->hasChanges($oldCoordinates, $newCoordinates);

                        if ($diffs) {
                            $diffsRegions[$explodedDescription[0]] = $explodedDescription;

                            if ($explodedDescription[1] == 'allow_zone') {
                                $zoneName = '-';
                                $regionName = $explodedDescription[2];
                                $filialCode = $explodedDescription[3];
                                $zoneCodeShort = null;
                                $zoneCode = null;
                            } else {
                                $zoneName = $explodedDescription[2] . ' ' . $explodedDescription[3];
                                $regionName = $explodedDescription[4];
                                $filialCode = $explodedDescription[5];
                                $zoneCode = $explodedDescription[3];
                                $zoneCodeShort = explode('_', $zoneCode)[1];
                            }

                            $changes[$polygon['properties']['description']] = [
                                'id' => $polygon['id'],
                                'zone_name' => $zoneName,
                                'zone_code' => $explodedDescription[3] ?? null,
                                'region_name' => $regionName,
                                'points' => $newCoordinates,
                                'region_id' => $explodedDescription[0],
                                'type' => $explodedDescription[1],
                                'filial_code' => sprintf('%50d', $filialCode),
                                'filial_id' => (int) $filialCode,
                                'code_short' => $zoneCodeShort,
                                'code' => $zoneCode,
                                'to_import' => false
                            ];
                        }
                    } else {
                        if (!isset($explodedDescription[1])) {
                            $notFoundPolygons[$explodedDescription[1]] = ['id' => $newPolygon['id']];
                        }
                    }
                }
            }

            Storage::put(self::UPDATED_FILE_NAME, json_encode($newData));
        } else {
            throw new Exception('Не найден файл с настройками из сервиса');
        }

        return ['changes' => array_values($changes), 'notFoundPolygons' => $notFoundPolygons, 'diffsRegions' => $diffsRegions];
    }

    /**
     * @throws Exception
     */
    public function exportFromDelivery(): array
    {
//        $allRegions = Region::all();
        // получаем регионы из коонфиг сервиса
        // чтобы исключить выключенные регионы
        $polygonsCoordinates = [];
        $allRegions = $this->krakenApi->configServiceRequest('config/regions');

        if (!isset($allRegions['regions'])) {
            throw new Exception("Doesn't regions exists from config service");
        }

        foreach ($allRegions as $region) {
            if (!$region->status) {
                continue;
            }

            $polygons = $this->krakenApi
                ->deliveryServiceRequest('settings/delivery-zones/find-all-by-hru-region-id', ['region_id' => $region->id]);
            $polygonsCoordinatesByRegion = $this->prepareCoordinates($polygons);
            $polygonsCoordinates = array_merge($polygonsCoordinatesByRegion, $polygonsCoordinates);
        }

        $allowZones = $this->krakenApi->deliveryServiceRequest('settings/allow-zones');
        $allowZonesCoordinates = $this->prepareCoordinates($allowZones, 'allow-zones');
        $polygons = array_merge($polygonsCoordinates, $allowZonesCoordinates);
        $polygonsData = $this->prepareDataForImportToFile($polygons);
        $jsonData = json_encode($polygonsData);

        if (Storage::exists(self::FILE_NAME)) {
            Storage::delete(self::FILE_NAME);
        }

        Storage::put(self::FILE_NAME, $jsonData);

        return $polygonsData;
    }
    public function importToService(array $zonesToUpdate, array $changedZones): void
    {
        if ($zonesToUpdate) {
//            $codes = [];

            // берём зоны, которые не отметили для отправки в сервис
            $filialIdsToUpdate = array_column($zonesToUpdate, 'filial_id');
            $filialIdsChangedZones = array_column($changedZones, 'filial_id');
            $notSelectedZoneCodes = array_diff($filialIdsChangedZones, $filialIdsToUpdate);
            $notSelectedZones = array_filter($changedZones, function ($zone) use ($notSelectedZoneCodes) {
                return in_array($zone['filial_id'], $notSelectedZoneCodes);
            });

            // Сначала изменяем координаты для всех зон
            foreach ($zonesToUpdate as &$zone) {
                foreach ($zone['points'] as &$point) {
                    list($point[0], $point[1]) = [$point[1], $point[0]];
                }

//                $codes[] = $zone['filial_code'];
            }

            $deliveryZones = array_filter($zonesToUpdate, function ($zone) {
                if ($zone['type'] == 'polygon') {
                    return true;
                }

                return false;
            });

            $allowZones = array_filter($zonesToUpdate, function ($zone) {
                if ($zone['type'] == 'allow_zone') {
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
                $notSelectedAllowZones = array_filter($changedZones, function ($zone) use ($notSelectedZoneCodes) {
                    if (in_array($zone['filial_id'], $notSelectedZoneCodes) && $zone['type'] == 'allow_zone') {
                        return true;
                    }

                    return false;
                });

                $notSelectedDeliveryZones = array_filter($changedZones, function ($zone) use ($notSelectedZoneCodes) {
                    if (in_array($zone['filial_id'], $notSelectedZoneCodes) && $zone['type'] == 'polygon') {
                        return true;
                    }

                    return false;
                });

                $preparedNotSelectedAllowZones = $this->prepareCoordinates($notSelectedAllowZones, 'allow-zones');
                $preparedNotSelectedDeliveryZones = $this->prepareCoordinates($notSelectedDeliveryZones);
                $preparedNotSelectedZones = array_merge($preparedNotSelectedDeliveryZones, $preparedNotSelectedAllowZones);
                $jsonData = json_encode($this->prepareDataForImportToFile($preparedNotSelectedZones));

                if (Storage::exists(self::FILE_NAME)) {
                    Storage::delete(self::FILE_NAME);
                }

                if (Storage::exists(self::UPDATED_FILE_NAME)) {
                    Storage::delete(self::UPDATED_FILE_NAME);
                }

                Storage::put(self::FILE_NAME, $jsonData);
            } else {
                Storage::delete(self::FILE_NAME);
                Storage::move(self::UPDATED_FILE_NAME, self::FILE_NAME);
            }
//            отпрака в кафу. Пока отложено.
//            $this->eventDispatcher->filialZoneChanged($codes);
        }
    }

    private function prepareCoordinates($polygons, string $type = 'polygons'): array
    {
        $data = [];

        foreach ($polygons as $polygon) {
            $regionId = isset($polygon['region']) ? $polygon['region']['id'] : $polygon['region_id'];
            $region = Region::where('region_id', $regionId)->first();
            $filial = $this->filialRepo->getByFilialId($polygon['filial_id']);
            $name = '';
            $fillOpacity = 0.6;
            $stroke = '82cdff';
            $strokeOpacity = 0.9;
            $regionId = '';

            if ($region) {
                $name = $region->name;
                $regionId = $region->region_id;
            }

            if ($filial) {
                $filialCode = $filial->filial_id;
            } else {
                $filialCode = $polygon['filial_id'];
            }

            if ($type === 'allow-zones') {
                $description = $regionId . '|allow_zone|' . $name . '|' . $filialCode;
                $fill = '#e6761b';
                $fillOpacity = 0.05;
                $stroke = '#ed4543';
                $strokeOpacity = 0.2;
            }  else {
                if ($polygon['code_short'] == 'B') {
                    $fillOpacity = 0.4;
                    $fill = 'ff931e';
                } elseif ($polygon['code_short'] == 'C') {
                    $fillOpacity = 0.2;
                    $fill = 'e6761b';
                } else {
                    $fill = 'ffd21e';
                }

                $description = $regionId . '|polygon|' . 'Зона|' . $polygon['code'] .  '|' . $name . '|' . $filialCode;
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
}
