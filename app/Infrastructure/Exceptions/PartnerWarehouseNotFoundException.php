<?php

namespace App\Infrastructure\Exceptions;

use Exception;

class PartnerWarehouseNotFoundException extends Exception
{
    protected $message = 'Не найден склад';
}