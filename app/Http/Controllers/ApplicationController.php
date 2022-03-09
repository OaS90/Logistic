<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function getList()
    {
        $list = Application::where('user_id', Auth::id())->get();
        return view('application-list', ['list' => $list]);
    }

    public function show()
    {
        return view('application-create', ['user' => Auth::user()]);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $data['delivery_time'] = $data['delivery_from'] . '-' . $data['delivery_till'];
        $data['elevator'] = false;
        unset($data['delivery_from']);
        unset($data['delivery_till']);
        unset($data['_token']);

        $newApplication = Application::create($data);

        return redirect()->back();
    }
}
