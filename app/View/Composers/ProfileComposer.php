<?php

namespace App\View\Composers;

//todo передалть на репозиторий
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileComposer
{

    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $user = Auth::user();
        $view->with('user', $user);
    }
}
