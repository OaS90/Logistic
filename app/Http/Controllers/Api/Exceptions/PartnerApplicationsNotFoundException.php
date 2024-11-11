<?php

namespace App\Http\Controllers\Api\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class PartnerApplicationsNotFoundException extends Exception
{
    public function __construct(private readonly string $partnerId)
    {
        parent::__construct();
    }

    public function render(): Response
    {
        $message = 'Не найдено заявок для клиента с идентификатором ';
        $status = Response::HTTP_UNPROCESSABLE_ENTITY;

        return response()
            ->json(
                ['message' => $message . $this->partnerId],
                $status, ['Content-type'=> 'application/json; charset=utf-8'],
                JSON_UNESCAPED_UNICODE
            );
    }
}