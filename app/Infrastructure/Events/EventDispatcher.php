<?php

namespace App\Infrastructure\Events;

use App\Domain\DTO\WarehouseShipmentSettingsDTO;
use Hrulibs\Events\Domain\DispatchesEvents;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Message\Message;

class EventDispatcher
{
    use DispatchesEvents;

    const BRANCH_OFFICE_MAP_CHANGE_ACTIONS = 'bort-udachi.branch-offices-map';
    const BRANCH_OFFICE_ACTIONS = 'bort-udachi.branch-office';


    public function filialZoneChanged(array $codes): void
    {
        try {
            $this->getPublisher()
                ->onTopic(self::BRANCH_OFFICE_MAP_CHANGE_ACTIONS)
                ->withMessage(new Message(body: ['codes' => $codes, 'action' => 'change']))
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
