<?php

namespace App\Http\Controllers\Api\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class JsonParseException extends Exception
{
    public function render(): Response
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Ошибка формата json';

        return response(['message' => $message, 'success' => false], $status);
    }
}
