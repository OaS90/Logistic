<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\QuotesController;
use App\Http\Controllers\Admin\QuoteEmailsController;
use App\Http\Controllers\Admin\TransportCompanySettingsController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\TariffController;
use App\Http\Controllers\Admin\AdminUserTariffsController;
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
    Route::crud('transport-company', 'TransportCompanyCrudController');
    Route::crud('transport-company-warehouse', 'TransportCompanyWarehouseCrudController');
    Route::get('transport-company-settings', [TransportCompanySettingsController::class, 'show']);
    Route::post('save-tc-settings', [TransportCompanySettingsController::class, 'save']);
    Route::get('export', [TransportCompanySettingsController::class, 'export']);
    Route::prefix('support')->group(function() {
        Route::get('apps', [SupportController::class, 'showAppsStatuses']);
        Route::get('apps/get', [SupportController::class, 'getApps']);
        Route::get('partners', [SupportController::class, 'getPartners']);
        Route::get('partner-warehouses', [SupportController::class, 'getPartnerWarehouses']);
        Route::get('show-upload-page', [SupportController::class, 'showUploadPage']);
        Route::post('import-app', [SupportController::class, 'upload']);
    });
    Route::crud('application-obi', 'ApplicationObiCrudController');
    Route::post('tariffs/{tariffId}/add-regions', [TariffController::class, 'addRegions']);
    Route::post('tariffs/{tariffId}/add-all-regions', [TariffController::class, 'addAllRegions']);
    Route::delete('tariffs/{tariffId}/delete-region/{regionId}', [TariffController::class, 'deleteRegion']);
    Route::get('tariffs/{tariffId}/edit/', [TariffController::class, 'edit']);
    Route::get('tariffs/{tariffId}/edit/regions/{regionId}/edit', [TariffController::class, 'regionEditShow']);
    Route::post('tariffs/{tariffId}/region/{regionId}/save', [TariffController::class, 'regionSave']);
    Route::post('tariffs/{tariffId}/region/{regionId}/delete-zone', [TariffController::class, 'zoneDelete']);
    Route::get('tariffs', [TariffController::class, 'list'])->name('tariff-list');
    Route::get('tariffs/get', [TariffController::class, 'getAll']);
    Route::post('tariffs/{tariffId}/clone', [TariffController::class, 'cloneTariff']);
    Route::post('tariffs/{tariffId}/request', [TariffController::class, 'permissionsRequest']);
    Route::get('tariffs/show', [TariffController::class, 'show']);
    Route::post('tariffs/create', [TariffController::class, 'create']);
    Route::delete('tariffs/{tariffId}/delete', [TariffController::class, 'delete']);
    Route::post('tariffs/{tariffId}/update-name-or-alias', [TariffController::class, 'updateNameOrAlias']);
    Route::crud('tariff-categories', 'TariffCategoriesCrudController');
//    Route::crud('tariff', 'TariffCrudController');
    Route::crud('tariff-zones', 'TariffZonesCrudController');
    Route::post('tariffs-holodilnik/get', [TariffController::class, 'getFromService']);
    Route::post('admin-user/tariff/permission-edit/add', [AdminUserTariffsController::class, 'addTariff']);
    Route::post('admin-user/tariff/permission-edit/remove', [AdminUserTariffsController::class, 'removeTariff']);
}); // this should be the absolute last line of this file