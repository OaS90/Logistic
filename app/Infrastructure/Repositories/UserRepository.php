<?php

namespace App\Infrastructure\Repositories;

use Illuminate\Support\Facades\Storage;

class UserRepository
{
    public function update($data, $user)
    {
       return $user->update($data);
    }

    public function deleteAvatar($user)
    {
        Storage::delete('public/' . $user->avatar);
        $user->update(['avatar' => '']);
    }
}
