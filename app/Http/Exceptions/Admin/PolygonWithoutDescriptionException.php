<?php

namespace App\Http\Exceptions\Admin;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class PolygonWithoutDescriptionException extends Exception
{
    protected $message = 'Не указано описание у нового полигона';
    protected $code = Response::HTTP_BAD_REQUEST;
}
