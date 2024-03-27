<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CrudTrait;

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function obiApplications(): HasMany
    {
        return $this->hasMany(ApplicationObi::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class, 'user_id', 'id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->firstname . ' ' . $this->patronymic . ' ' . $this->lastname;
    }

    public function getPhoneAttribute(): string
    {
        return '+7 (' . substr($this->mobile_phone, 0, 3) . ') ' .
            substr($this->mobile_phone, 3, 3) . '-' .
            substr($this->mobile_phone, 6, 2) . '-' .
            substr($this->mobile_phone, 8);
    }
}
