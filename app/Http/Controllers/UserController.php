<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function editForm()
    {
        return view('profile-edit', ['user' => Auth::user()]);
    }

    public function save(Request $request)
    {
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $fileName = $request->file('avatar')->getClientOriginalName();
            $avatarPath = $request->file('avatar')->storeAs('avatars', $fileName, 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        $user->update($request->except(['_token', 'avatar']));

        return redirect()->back();
    }

    public function avatarDelete(): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        Storage::delete('public/' . $user->avatar);
        $user->update(['avatar' => '']);

        return redirect()->back();
    }
}
