<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $table = 'quotes';
    protected $guarded = ['id'];
    protected $casts = [
        'days' => 'array',
        'delivery_hours' => 'array'
    ];

    public function intervals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IntervalQuote::class, 'quote_id', 'id');
    }

    public function region(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Region::class, 'id', 'division_id');
    }
}
