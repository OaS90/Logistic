<?php

namespace App\Http\Controllers\Api\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApplicationNotFoundException extends Exception
{
    public function __construct(private readonly string $orderNumber)
    {
        parent::__construct();
    }

    public function render(): Response
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Не найдена заявка с номером заказа ';

        response()
            ->json(
                ['message' => $message . $this->orderNumber], $status,
                ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
            );
    }
}