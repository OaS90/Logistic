<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuoteWarehouse extends Model
{
    use CrudTrait;

    protected $table = 'quote_warehouse';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function regions(): BelongsToMany
    {
        return $this->BelongsToMany(Region::class, 'quote_warehouse_region', 'quote_warehouse_id', 'region_id');
    }
}
