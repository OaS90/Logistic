<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationApiUpdatedStatusesResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $data = [];

        foreach ($this->resource as $statusInfo) {
            $data[] = [
                'id' => $statusInfo['id'],
                'success' => $statusInfo['success'],
                'message' => $statusInfo['message']
            ];
        }

        return $data;
    }
}
