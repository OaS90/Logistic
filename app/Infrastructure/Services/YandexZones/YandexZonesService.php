<?php

namespace App\Infrastructure\Services\YandexZones;

use App\Infrastructure\Services\Kraken\Api;
use App\Models\Region;
use Illuminate\Support\Facades\Storage;

class YandexZonesService
{
    public function __construct(protected readonly Api $krakenApi) {}

    public function importFromDelivery(array $newData): array
    {
        $filename = 'zones.json';

        if (Storage::exists($filename)) {
            $contents = Storage::get($filename);
            $oldData = json_decode($contents, true);
            $changes = [];
            $notFoundPolygons = [];
            $diffsRegions = [];
            foreach ($newData['features'] as $newPolygon) {
                $newCoordinates = $newPolygon['geometry']['coordinates'][0];
                $newDescription = trim($newPolygon['properties']['description']);

                foreach ($oldData['features'] as $polygon) {
                    $oldCoordinates = $polygon['geometry']['coordinates'][0];
                    $explodedDescription = explode('-', $polygon['properties']['description']);

                    if ($polygon['properties']['description'] == $newDescription) {
                        $diffs = $this->hasChanges($oldCoordinates, $newCoordinates);

                        if ($diffs) {
                            $diffsRegions[$explodedDescription[0]] = $explodedDescription;
                            $zoneName = $explodedDescription[1] == 'allow_zone' ? '-' : $explodedDescription[2] . ' ' . $explodedDescription[3];
                            $regionName = $explodedDescription[1] == 'allow_zone' ? $explodedDescription[2] : $explodedDescription[4];
                            $changes[$polygon['properties']['description']] = [
                                'zone_name' => $zoneName,
                                'zone_code' => $explodedDescription[3] ?? null,
                                'region_name' => $regionName,
                                'points' => $newCoordinates,
                                'region_id' => $explodedDescription[0],
                                'type' => $explodedDescription[1],
                                'erp_warehouse_id' => $explodedDescription[5] ?? null,
                                'warehouse_alias' => $explodedDescription[6] ?? null,
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
        $senderZones = $this->krakenApi->deliveryServiceRequest('settings/sender-zones');
        $senderZonesCoordinates = $this->prepareCoordinates($senderZones, 'sender-zones');
        $data['type'] = 'FeatureCollection';
        $data['metadata'] = [
            'name' => 'maps',
            'creator' => 'BoF Holodilnik'
        ];
        $data['features'] = array_merge($polygonsCoordinates, $allowZonesCoordinates, $senderZonesCoordinates);
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
                    $this->krakenApi->deliveryServiceRequest('settings/delivery-zones/', $deliveryZones, 'PATCH');
                }
            }

            if ($allowZones) {
                foreach ($allowZones as $allowZone) {
                    $allowZone['region_id'] = (int) $allowZone['region_id'];
                    $this->krakenApi->deliveryServiceRequest('settings/allow-zones', $allowZone, 'PATCH');
                }
            }
        }
    }

    private function prepareCoordinates($polygons, string $type = 'polygons'): array
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

            if ($region) {
                $name = $region->name;
                $regionId = $region->region_id;
            }

            if ($type === 'allow-zones') {
                $description = $regionId . '-allow_zone-' . $name;
                $fill = '#e6761b';
                $fillOpacity = 0.05;
                $stroke = '#ed4543';
                $strokeOpacity = 0.2;
            } elseif ($type == 'sender-zones') {
                $description = $regionId  . '-sender_zone-' . 'Зона-' . $polygon['zone_code'] .
                    '-' . trim($name) . '-' . $polygon['erp_warehouse_id'] . '-' . $polygon['warehouse_alias'];

                if ($polygon['zone_code'] == 'A') {
                    $fillOpacity = 0.3;
                    $fill = 'ff931c';
                } elseif ($polygon['zone_code'] == 'B') {
                    $fillOpacity = 0.1;
                    $fill = 'e6761e';
                } else {
                    $fill = 'ffd21e';
                }
            } else {
                if ($polygon['code_short'] == 'B') {
                    $fillOpacity = 0.4;
                    $fill = 'ff931e';
                } elseif ($polygon['code_short'] == 'C') {
                    $fillOpacity = 0.2;
                    $fill = 'e6761b';
                } else {
                    $fill = 'ffd21e';
                }

                $description = $regionId . '-polygon-' . 'Зона-' . $polygon['code'] .  '-' . $name;
            }

            foreach ($polygon['points'] as &$point) {
                list($point[0], $point[1]) = [$point[1], $point[0]];
            }

            $data[] = [
                'type' => 'Feature',
                'id' => $polygon['id'] - 1,
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [$polygon['points']]
                ],
                'properties' => [
                    'description' => trim($description),
                    'fill' => $fill,
                    'fill-opacity' => $fillOpacity,
                    'stroke' => $stroke,
                    'stroke-width' => '5',
                    'stroke-opacity' => $strokeOpacity
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
