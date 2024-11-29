<?php

namespace App\Http\Controllers\Api\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends Exception
{
    public function __construct(private readonly string $partnerId)
    {
        parent::__construct();
    }

    public function render(): Response
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Не найден пользователь с идентификатором ' . $this->partnerId;

        return response()->json(['message' => $message],
            $status, ['Content-type' => 'application/json; charset=utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }
}