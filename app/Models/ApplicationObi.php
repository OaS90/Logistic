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
}