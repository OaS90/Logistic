<?php

namespace App\Providers;

use App\Application\ApplicationServiceInterface;
use App\Infrastructure\Services\Dadata\DadataService;
use App\Infrastructure\Repositories\ApplicationObiRepository;
use App\Infrastructure\Repositories\ApplicationRepository;
use App\Infrastructure\Repositories\AppStatusHistoryRepository;
use App\Infrastructure\Repositories\DeliveryAddressRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\WarehouseRepository;
use App\Infrastructure\Services\Application\ApplicationCheckService;
use App\Infrastructure\Services\Application\ApplicationService;
use App\Infrastructure\Services\Application\Factories\ApplicationFactory;
use App\Infrastructure\Services\Application\Factories\ProductFactory;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\UrlGenerator;
use Picqer\Barcode\BarcodeGeneratorDynamicHTML;
use App\Infrastructure\Services\Dadata\Api\Api as DadataApi;
use App\Infrastructure\Services\Kraken\Api as KrakenApi;

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
        $this->app->bind(ApplicationServiceInterface::class, function (Application $app) {
            return new ApplicationService(
                appRepo: $this->app->make(ApplicationRepository::class),
                appObiRepo: $this->app->make(ApplicationObiRepository::class),
                productRepo: $this->app->make(ProductRepository::class),
                codeGenerator: $this->app->make(BarcodeGeneratorDynamicHTML::class),
                deliveryAddressRepo: $this->app->make(DeliveryAddressRepository::class),
                appCheckService: $this->app->make(ApplicationCheckService::class),
                addressRepo: $this->app->make(DeliveryAddressRepository::class),
                warehouseRepo: $this->app->make(WarehouseRepository::class),
                appStatusHistoryRepo: $this->app->make(AppStatusHistoryRepository::class),
                userRepo: $this->app->make(UserRepository::class),
                applicationObiRepo: $this->app->make(ApplicationObiRepository::class),
                productFactory: $this->app->make(ProductFactory::class),
                appFactory: $this->app->make(ApplicationFactory::class),
                dadataService: $this->app->make(DadataService::class),
                krakenApi: $this->app->make(KrakenApi::class)
            );
        });

        $this->app->bind(KrakenApi::class, function () {
            return new KrakenApi(
                new Client([
                    'base_uri' => config('services.kraken.uri'),
                    RequestOptions::HEADERS =>  [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'x-kraken-authorization' => config('services.kraken.token'),
                    ]
                ])
            );
        });

        $this->app->bind(
            \Backpack\PermissionManager\app\Http\Controllers\UserCrudController::class,
            \App\Http\Controllers\Admin\UserCrudController::class
            );

        $this->app->bind(DadataApi::class, function () {
            return new DadataApi(
                suggestClient: new Client([
                    'base_uri' => config('services.dadata.suggest_url'),
                    RequestOptions::HEADERS => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'Token ' . config('services.dadata.token')
                    ]
                ]),
                cleanClient: new Client([
                    'base_uri' => config('services.dadata.clean_url'),
                    RequestOptions::HEADERS => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'Token ' . config('services.dadata.token'),
                        'X-Secret' => config('services.dadata.secret')
                    ]
                ])
            );
        });
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
