<?php

namespace App\Http\Controllers\Api\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class StatusUpdateException extends Exception
{
    public function getError($e, int $appId): array
    {
        return match ($e) {
            $e instanceof ModelNotFoundException => [
                'id' => $appId,
                'success' => false,
                'message' => 'Не найден заказ с номером ' . $appId
            ],
            default => [
                'id' => $appId,
                'success' => false,
                'message' => 'Произошла непредвиденная ошибка'
            ],
        };
    }
}

