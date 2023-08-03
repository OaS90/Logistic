<?php

namespace App\Models;

use App\Shared\Eloquent\ConvertsToUtfTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use CrudTrait;
    use ConvertsToUtfTrait;

    protected $table = 'applications';
    protected $guarded = ['id'];
    const STATUSES = [
        'created' => 'Создано',
        'new' => 'Новый',
        'inProgress' => 'В работе',
        'loaded' => 'Загружен',
        'postponed' => 'Отложен',
        'refusal' => 'Отказ',
        'completed' => 'Выполнен',
        'defect' => 'Брак'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(DeliveryAddress::class, 'delivery_address', 'id');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'app_id', 'id');
    }

    public function getFullAddressAttribute()
    {
        return implode(', ', [
            $this->address->city_name,
            $this->address->region_name,
            $this->address->street,
            $this->address->building,
            $this->address->entrance ? 'п. ' . $this->address->entrance : '',
            $this->address->floor ? 'этаж ' . $this->address->floor : '',
            $this->address->flat ? 'кв. ' . $this->address->flat : ''
        ]);
    }

    public function getTotalCostAttribute()
    {
        $total = 0;

        foreach ($this->products as $product) {
            $total += $product->cost;
        }

        return $total;
    }

    public function getMobilePhoneAttribute(): string
    {
        return '+7 ' . substr($this->client_phone, 0, 3) . ' ' .
            substr($this->client_phone, 3, 3) . '-' .
            substr($this->client_phone, 6, 2) . '-' .
            substr($this->client_phone, 8);
    }

    public function getStatus($status): string
    {
        if ($status)
            return self::STATUSES[$status];

        return '';
    }

    public function getParsedDeliveryDateAttribute()
    {
        $deliveryDate = $this->hru_delivery_date ?? $this->delivery_date;
        return Carbon::createFromDate($deliveryDate)->format('d.m.Y');
    }
}
