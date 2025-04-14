<?php

namespace App\Infrastructure\Events;

use Hrulibs\Events\Domain\DispatchesEvents;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Message\Message;

class EventDispatcher
{
    use DispatchesEvents;

    public function filialZoneChanged(array $codes): void
    {
        try {
            $this->getPublisher()
                ->onTopic('filials_changes')
                ->withMessage(new Message(body: ['codes' => $codes]))
                ->send();
        } catch (\Exception $e) {
            Log::error('Event changing filial zone error ', ['exception' => $e]);
        }
    }
}
