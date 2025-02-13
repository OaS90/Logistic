<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Infrastructure\Repositories\UserRepository;
use Illuminate\View\View;

class UserController extends Controller
{
    protected $repo;

    public function __construct(UserRepository $userRepository)
    {
        $this->repo = $userRepository;
    }

    public function show(): View
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function editForm(): View
    {
        return view('profile-edit', ['user' => Auth::user()]);
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $data = $request->except(['_token']);
        $userData = json_decode($data['user'], true);
        $userData['mobile_phone'] = parse_phone($data['phone']);

        if ($request->hasFile('avatar')) {
            $fileName = $request->file('avatar')->getClientOriginalName();
            $avatarPath = $request->file('avatar')->storeAs('avatars', $fileName, 'public');
            $userData['avatar'] = $avatarPath;
        }

        $this->repo->update($userData, $user);
        // нужно ли будет выводить попап?
        return redirect()->back();
    }

    public function avatarDelete(): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $this->repo->deleteAvatar($user);
        // нужно ли будет выводить попап?
        return redirect()->back();
    }
}
