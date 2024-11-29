<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationStatusHistoryResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $data = [];

        foreach ($this->resource as $statusInfo) {
            $data[] = [
                'orderId' => $statusInfo['orderId'],
                'statuses' => $statusInfo['statuses']
            ];
        }

        return $data;
    }
}
