<?php

namespace App\Infrastructure\Services\YandexZones;

use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Services\Kraken\Api;
use App\Models\Region;
use Illuminate\Support\Facades\Storage;

class YandexZonesService
{
    const FILE_NAME = 'zones.json';
    const UPDATED_FILE_NAME = 'updated_zones.json';
    public function __construct(private readonly Api $krakenApi, private readonly FilialRepository $filialRepo) {}

    /**
     * @throws \Exception
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
                            $zoneName = $explodedDescription[1] == 'allow_zone' ? '-' : $explodedDescription[2] . ' ' . $explodedDescription[3];
                            $regionName = $explodedDescription[1] == 'allow_zone' ? $explodedDescription[2] : $explodedDescription[4];
                            $filialCode = $explodedDescription[1] == 'allow_zone' ? $explodedDescription[3] : $explodedDescription[5];
                            $changes[$polygon['properties']['description']] = [
                                'zone_name' => $zoneName,
                                'zone_code' => $explodedDescription[3] ?? null,
                                'region_name' => $regionName,
                                'points' => $newCoordinates,
                                'region_id' => $explodedDescription[0],
                                'type' => $explodedDescription[1],
                                'filial_code' => $filialCode,
                                'filial_id' => (int) $filialCode,
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

            Storage::disk('local')->put(self::UPDATED_FILE_NAME, json_encode($newData));
        } else {
            throw new \Exception('Не найден файл с настройками из сервиса');
        }

        return ['changes' => array_values($changes), 'notFoundPolygons' => $notFoundPolygons, 'diffsRegions' => $diffsRegions];
    }

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
//        $senderZones = $this->krakenApi->deliveryServiceRequest('settings/sender-zones');
//        $senderZonesCoordinates = $this->prepareCoordinates($senderZones, 'sender-zones');
        $data['type'] = 'FeatureCollection';
        $data['metadata'] = [
            'name' => 'maps',
            'creator' => 'BoF Holodilnik'
        ];
        $data['features'] = array_merge($polygonsCoordinates, $allowZonesCoordinates);
        $filename = 'zones.json';
        $jsonData = json_encode($data);

        if (Storage::exists($filename)) {
            Storage::disk('local')->delete($filename);
        }

        Storage::disk('local')->put($filename, $jsonData);

        return $data;
    }


    public function importToService(array $zonesToUpdate): void
    {
        if ($zonesToUpdate) {

            // Сначала изменяем координаты для всех зон
            foreach ($zonesToUpdate as &$zone) {
                foreach ($zone['points'] as &$point) {
                    list($point[0], $point[1]) = [$point[1], $point[0]];
                }
            }

            $senderZones = array_filter($zonesToUpdate, function ($zone) {
                if ($zone['type'] == 'sender_zone') {
                    return true;
                }

                return false;
            });

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

            if ($senderZones) {
                foreach ($senderZones as $senderZone) {
                    $this->krakenApi->deliveryServiceRequest('settings/sender-zones', $senderZone, 'PATCH');
                }
            }

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

            Storage::disk('local')->delete(self::FILE_NAME);
            Storage::disk('local')->move(self::UPDATED_FILE_NAME, self::FILE_NAME);
        }
    }

    private function prepareCoordinates($polygons, string $type = 'polygons'): array
    {
        $data = [];

        foreach ($polygons as $polygon) {
            $regionId = isset($polygon['region']) ? $polygon['region']['id'] : $polygon['region_id'];
            $region = Region::where('region_id', $regionId)->first();
            $filialCode =  sprintf("%05d", $polygon['filial_id']);
            $filial = $this->filialRepo->getByCode($filialCode);
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
                $filialCode = $filial->code;
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
}
