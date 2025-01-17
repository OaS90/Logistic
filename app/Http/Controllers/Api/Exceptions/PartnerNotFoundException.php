<?php

namespace App\Http\Controllers\Api\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class PartnerNotFoundException extends Exception
{
    public function __construct(private readonly string $partnerId)
    {
        parent::__construct();
    }

    public function render(): Response
    {
        $status = Response::HTTP_UNPROCESSABLE_ENTITY;
        $message = 'Не найден партнёр с идентификатором ';

        return response()
            ->json(
                ['message' => $message . $this->partnerId],
                $status, ['Content-type'=> 'application/json; charset=utf-8'],
                JSON_UNESCAPED_UNICODE
            );
    }
}
