<?php

namespace App\Http\Controllers\Api\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class WarehouseNotFoundException extends Exception
{
    public function render(): Response
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Не найден склад';

        return response()
            ->json(['message' => $message], $status,
                ['Content-type'=> 'application/json; charset=utf-8'],
                JSON_UNESCAPED_UNICODE
            );
    }
}