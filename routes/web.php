<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();
Route::get('test', function () {
    dd(0.7 * 1000);
    $arr = [
        // Москва
        '00181' => [
            'division_id' => 1,
            'region_id' => 1
        ],
        '00379' => [
            'division_id' => 2,
            'region_id' => 1
        ],
        '00385' => [
            'division_id' => 2,
            'region_id' => 1
        ]
    ];

    $fp = fopen(storage_path(''), 'w');
});
Route::get('api-documentation', function () {
    return view('common-api-description');
})->name('api-description');
Route::get('documentation', function () {
    return view('documentation');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('application-list', [ApplicationController::class, 'getList'])->name('application-list');
    Route::get('application', [ApplicationController::class, 'show'])->name('application');
    Route::get('application/{id}', [ApplicationController::class, 'current'])->name('application-show');
    Route::get('profile', [UserController::class, 'show'])->name('profile');
    Route::get('profile-edit', [UserController::class, 'editForm'])->name('profile-edit-form');
    Route::post('profile-save', [UserController::class, 'update'])->name('profile-save');
    Route::get('avatar-delete', [UserController::class, 'avatarDelete'])->name('avatar-delete');
    Route::post('application-create', [ApplicationController::class, 'create'])->name('application-create');
    Route::post('get-address', [ApplicationController::class, 'getAddress']);
    Route::post('import-app', [ApplicationController::class, 'import']);
    Route::get('application/{id}/sticker', [ApplicationController::class, 'makeSticker'])->name('make-sticker');
    Route::get('api-description', function () {
        return view('api-description');
    })->name('api-description');
    Route::get('download-csv-example', [ApplicationController::class, 'downloadFileExample']);
});

