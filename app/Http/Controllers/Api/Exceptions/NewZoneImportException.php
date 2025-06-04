<?php

namespace App\Http\Controllers\Api\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Exception;

class NewZoneImportException extends Exception
{
    public function __construct(private readonly array $zoneInfo)
    {
        parent::__construct();
    }

    public function render(): Response
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Не удалось сохранить новую зону в сервисе. Данные зоны: ';

        return response()
            ->json(
                ['message' => $message . implode(', ', $this->zoneInfo), 'success' => false], $status,
                ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
            );
    }
}
