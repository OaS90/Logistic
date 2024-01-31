<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\QuotesController;
use App\Http\Controllers\Admin\QuoteEmailsController;
use App\Http\Controllers\Admin\SupportController;
// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.
Route::group([
        'prefix' => config('backpack.base.route_prefix'),
        'middleware' => config('backpack.base.web_middleware', 'web'),
    ], function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('backpack.auth.login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('logout', [LoginController::class, 'logout'])->name('backpack.auth.logout');
    Route::post('logout', [LoginController::class, 'logout']);

    // Registration Routes...
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('backpack.auth.register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('partners', 'PartnerCrudController');
    Route::crud('applications', 'ApplicationCrudController');
    Route::crud('warehouses', 'WarehouseCrudController');
    Route::get('quotes', [QuotesController::class, 'show']);
    Route::post('save-quotes', [QuotesController::class, 'save']);
    Route::get('quote-emails', [QuoteEmailsController::class, 'show']);
    Route::post('save-quote-emails', [QuoteEmailsController::class, 'save']);
    Route::post('delete-quote-email/{id}', [QuoteEmailsController::class, 'delete']);
    Route::get('download-excel', [QuotesController::class, 'download']);
    Route::crud('regions', 'RegionCrudController');
    Route::crud('quote-warehouse', 'QuoteWarehouseCrudController');
    Route::prefix('support')->group(function() {
        Route::get('apps', [SupportController::class, 'showAppsStatuses']);
        Route::get('apps/get', [SupportController::class, 'getApps']);
        Route::get('partners', [SupportController::class, 'getPartners']);
        Route::get('partner-warehouses', [SupportController::class, 'getPartnerWarehouses']);
        Route::get('show-upload-page', [SupportController::class, 'showUploadPage']);
        Route::post('import-app', [SupportController::class, 'upload']);
    });
}); // this should be the absolute last line of this file