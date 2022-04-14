<?php

namespace App\Http\Controllers\Api\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class StatusUpdateException extends Exception
{
    public function getError($e, int $appId): array
    {
        switch ($e) {
            case $e instanceof ModelNotFoundException:
                return [
                    'id' => $appId,
                    'success' => false,
                    'message' => 'Не найден заказ с номером ' . $appId
                ];
                break;
            default:
                return [
                    'id' => $appId,
                    'success' => false,
                    'message' => 'Произошла непредвиденная ошибка'
                ];
        }
    }
}

