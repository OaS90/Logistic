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

        $zoneData = [
            'region_id' => $this->zoneInfo['region_id'],
            'zone' => $this->zoneInfo['zone'],
            'filial_id' => $this->zoneInfo['filial_id'],
        ];

        return response()
            ->json(
                ['message' => $message . implode(', ', $zoneData), 'success' => false], $status,
                ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
            );
    }
}
