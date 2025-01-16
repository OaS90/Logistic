<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Domain\ApplicationStatuses;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationObi extends Model
{
    use CrudTrait;
    use ApplicationStatuses;

    public const DEFAULT_PAYMENT_TYPE = 'Предоплата';
    public const DEFAULT_STORE_ID = 14010;

    protected $table = 'applications_obi';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(ObiProduct::class, 'app_id', 'id');
    }

    public function getMobilePhoneAttribute(): string
    {
        $explodedPhone = explode(', ', $this->phone);
        $parsedPhone = '';
        $phones = [];

        if (is_array($explodedPhone) && count($explodedPhone) > 1) {
            foreach ($explodedPhone as $phone) {
                $phones[] = '+7 ' . substr($phone, 0, 3) . ' ' .
                    substr($phone, 3, 3) . '-' .
                    substr($phone, 6, 2) . '-' .
                    substr($phone, 8);

                $parsedPhone = implode(', ', $phones);
            }
        } else {
            $parsedPhone = '+7 ' . substr($this->phone, 0, 3) . ' ' .
                substr($this->phone, 3, 3) . '-' .
                substr($this->phone, 6, 2) . '-' .
                substr($this->phone, 8);
        }

        return $parsedPhone;
    }

    public function getParsedDeliveryDateAttribute(): string
    {
        $deliveryDate = $this->hru_delivery_date ?? $this->delivery_date;
        return Carbon::createFromDate($deliveryDate)->format('d.m.Y');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalCost()
    {
        return $this->getAttribute('products_cost');
    }

    public function getTotalWeight()
    {
        return $this->getAttribute('order_weight');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     * Получение всех товаров исключая Доставку и Доплату
     */
    public function getProductsWithoutExtraPays(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->products()
            ->whereRaw('LOWER(name) NOT LIKE ?', ['%' . strtolower('доплата') . '%'])
            ->whereRaw('LOWER(name) NOT LIKE ?', ['%' . strtolower('доставка') . '%'])
            ->get();
    }
}