<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationApiCreateResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        if (count($this->resource) > 1) {
            return [
                'codes' => $this->resource,
                'success' => true,
                'message' => ''
            ];
        } else {
            return [
                'code' => $this->resource[0],
                'success' => true,
                'message' => ''
            ];
        }
    }
}
