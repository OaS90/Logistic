<?php

namespace App\Http\Controllers\Api\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Exception;

class DeletedZoneImportException extends Exception
{
    public function __construct(private readonly array $zoneInfo)
    {
        parent::__construct();
    }

    public function render(): Response
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Не удалось удлаить зону в сервисе. Данные зоны: ';
        $zoneData = [
            'id' => $this->zoneInfo['id'],
            'region_name' => $this->zoneInfo['region_name'],
            'zone_code' => $this->zoneInfo['zone_code'],
            'filial_id' => $this->zoneInfo['filial_id'],
            'code_short' => $this->zoneInfo['code_short'],
        ];

        return response()
            ->json(
                ['message' => $message . implode(', ', $zoneData), 'success' => false], $status,
                ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE
            );
    }
}
