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

    const BRANCH_OFFICE_MAP_CHANGE_ACTIONS = 'bort-udachi.branch-office-map';
    const BRANCH_OFFICE_ACTIONS = 'bort-udachi.branch-office';


    public function filialZoneChanged(string $code): void
    {
        try {
            $this->getPublisher()
                ->onTopic(self::BRANCH_OFFICE_MAP_CHANGE_ACTIONS)
                ->withMessage(new Message(body: ['code' => $code, 'action' => 'change_zone']))
                ->withKafkaKey($code)
                ->send();
        } catch (\Exception $e) {
            Log::error('Event changing filial zone error ', ['exception' => $e]);
        }
    }

    public function filialZoneCreated(array $newZone): void
    {
        try {
            $this->getPublisher()
                ->onTopic(self::BRANCH_OFFICE_MAP_CHANGE_ACTIONS)
                ->withMessage(new Message(body: ['zone' => $newZone, 'action' => 'create_zone']))
                ->withKafkaKey($newZone['filial_code'])
                ->send();
        } catch (\Exception $e) {
            Log::error('Event creating filial zone error ', ['exception' => $e]);
        }
    }

    public function filialZoneDeleted(array $deleteZone): void
    {
        try {
            $this->getPublisher()
                ->onTopic(self::BRANCH_OFFICE_MAP_CHANGE_ACTIONS)
                ->withMessage(new Message(body: [
                    'id' => $deleteZone['filial_code'],
                    'action' => 'delete_zones',
                    'zone' => $deleteZone
                ]))
                ->withKafkaKey($deleteZone['filial_code'])
                ->send();
        } catch (\Exception $e) {
            Log::error('Event changing filial zone error ', ['exception' => $e]);
        }
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
                ->onTopic(self::BRANCH_OFFICE_ACTIONS)
                ->withMessage(new Message(body: [
                    'id' => $dto->filialCode,
                    'action' => 'change_departure_points_transport_companies',
                    'data' => [$data]
                ]))
                ->withKafkaKey($dto->filialCode)
                ->send();
        } catch (\Exception $e) {
            Log::error('Event changing filial shipment filial error ', ['exception' => $e]);
        }
    }
}
