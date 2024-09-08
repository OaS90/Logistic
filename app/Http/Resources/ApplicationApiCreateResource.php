<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationApiCreateResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'codes' => $this->resource,
            'success' => true,
            'message' => ''
        ];
    }
}
