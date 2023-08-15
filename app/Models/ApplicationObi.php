<?php

namespace App\Models;

use App\Domain\ApplicationStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationObi extends Model
{
    use ApplicationStatuses;

    protected $table = 'applications_obi';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(ObiProduct::class, 'app_id', 'id');
    }
}