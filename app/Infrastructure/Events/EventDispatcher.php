<?php

namespace App\Infrastructure\Events;

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
    }
}
