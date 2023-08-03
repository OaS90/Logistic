<?php

namespace App\Models;

use App\Shared\Eloquent\ConvertsToUtfTrait;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use ConvertsToUtfTrait;

    protected $table = 'application_products';
    protected $guarded = ['id'];

    public function getNameAttribute($value): string
    {
        return $this->toUtf($value);
    }
}
