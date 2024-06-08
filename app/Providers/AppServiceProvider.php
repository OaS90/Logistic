<?php

namespace App\Providers;

use App\Application\ApplicationServiceInterface;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Services\Application\ApplicationService;
use App\Infrastructure\Services\Delivery\Api\Api;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\UrlGenerator;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // TODO сделать интерфейсы для репозиториев и забайндить их здесь
        $this->app->bind(ApplicationServiceInterface::class, function () {
            return new ApplicationService(
                appRepo: new ApplicationRepository(),
                appObiRepo: new ApplicationObiRepository(),
                productRepo: new ProductRepository(),
                codeGenerator: new BarcodeGeneratorDynamicHTML(),
                deliveryAddressRepo: new DeliveryAddressRepository
            );
        });

        $this->app->bind(Api::class, function () {
            return new Api(
                new Client([
                    'base_uri' => config('services.delivery_holodilnik_service.uri')
                ])
            );
        });

        $this->app->bind(
            \Backpack\PermissionManager\app\Http\Controllers\UserCrudController::class,
            \App\Http\Controllers\Admin\UserCrudController::class
            );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url): void
    {
        Schema::defaultStringLength(191);

        if(config('app.env') === 'production') {
            $url->forceScheme('https');
        }
    }
}
