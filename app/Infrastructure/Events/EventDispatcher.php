<?php

namespace App\Infrastructure\Events;

use App\Domain\DTO\WarehouseShipmentSettingsDTO;
use Hrulibs\Events\Domain\DispatchesEvents;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Message\Message;
use const Widmogrod\Functional\concat;

class EventDispatcher
{
    use DispatchesEvents;

    const FILIAL_ACTIONS_TOPIC = 'bort-udachi.branch-office';

    public function filialZoneChanged(array $codes): void
    {
        try {
            $this->getPublisher()
                ->onTopic(self::FILIAL_ACTIONS_TOPIC)
                ->withMessage(new Message(body: ['codes' => $codes, 'action' => 'change']))
                ->send();
        } catch (\Exception $e) {
            Log::error('Event changing filial zone error ', ['exception' => $e]);
        }
    }

    public function filialZoneCreated(array $newZones): void
    {
        try {
            $this->getPublisher()
                ->onTopic(self::FILIAL_ACTIONS_TOPIC)
                ->withMessage(new Message(body: ['zones' => $newZones, 'action' => 'create']))
                ->send();
        } catch (\Exception $e) {
            Log::error('Event creating filial zone error ', ['exception' => $e]);
        }
        $this->filialZoneChanged(array_map(function ($zone) {
            return sprintf('%05d', $zone['filial_id']);
        }, $newZones));
    }

    public function filialZoneDeleted(array $deletedZones): void
    {
        try {
            $this->getPublisher()
                ->onTopic(self::FILIAL_ACTIONS_TOPIC)
                ->withMessage(new Message(body: ['zones' => $deletedZones, 'action' => 'delete']))
                ->send();
        } catch (\Exception $e) {
            Log::error('Event changing filial zone error ', ['exception' => $e]);
        }
        $this->filialZoneChanged(array_map(function ($zone) {
            return sprintf('%05d', $zone['filial_id']);
        }, $deletedZones));
    }

    /**
     * Метод отправляет в кафку настройки складов отгрузки по филиалу
     * Event sourcing pattern
     */
    public function warehouseShipmentSettings(WarehouseShipmentSettingsDTO $dto): void
    {
        try {
            $data['departure_point_id'] = $dto->departurePointId;
            $data['transport_company_code'] = $dto->transportCompanyCode;
            $data['parameters'] = $dto->settings;

            $this->getPublisher()
                ->onTopic(self::FILIAL_ACTIONS_TOPIC)
                ->withMessage(new Message(body: [
                    'id' => $dto->filialCode,
                    'action' => 'change_departure_points_transport_companies',
                    'data' => $data
                ]))
                ->send();
        } catch (\Exception $e) {
            Log::error('Event changing filial shipment filial error ', ['exception' => $e]);
        }
    }
}
