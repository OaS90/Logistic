<?php

namespace App\Infrastructure\Repositories;

use App\Models\User;
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

    public function getBy1cId($id)
    {
        return User::where('id_1c', $id)->first();
    }

    public function getOrderByNumberAndUser1cId($userId, $orderId)
    {
        return User::where('id_1c', $userId)->first()
            ->applications()
            ->where('order_number', $orderId)
            ->first();
    }
}
