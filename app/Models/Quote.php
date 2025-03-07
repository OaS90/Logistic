<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Hru\Filial;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    use HasFactory;

    protected $table = 'quotes';
    protected $guarded = ['id'];
    protected $casts = [
        'days' => 'array',
        'delivery_hours' => 'array'
    ];

    public function intervals(): HasMany
    {
        return $this->hasMany(IntervalQuote::class, 'quote_id', 'id');
    }

    public function region(): HasOne
    {
        return $this->hasOne(Region::class, 'id', 'division_id');
    }

    public function filial(): BelongsTo
    {
        return $this->belongsTo(Filial::class, 'filial_id', 'id');
    }
}
