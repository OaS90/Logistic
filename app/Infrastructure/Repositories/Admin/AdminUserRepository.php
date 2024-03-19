<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Collection;

class AdminUserRepository
{
    public function getAll(): Collection
    {
        return AdminUser::all();
    }

    public function getById(int $userId)
    {
        return AdminUser::find($userId);
    }
}