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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('application-list', [ApplicationController::class, 'getList'])->name('application-list');
Route::get('application', [ApplicationController::class, 'show'])->name('application');
Route::get('profile', [UserController::class, 'show'])->name('profile');
Route::get('profile-edit', [UserController::class, 'editForm'])->name('profile-edit-form');
Route::post('profile-save', [UserController::class, 'update'])->name('profile-save');
Route::get('avatar-delete', [UserController::class, 'avatarDelete'])->name('avatar-delete');
Route::post('application-create', [ApplicationController::class, 'create'])->name('application-create');

