<?php

namespace App\Providers;

use App\Infrastructure\Services\ConfigService\Api\ConfigServiceApi;
use App\Infrastructure\Services\Delivery\Api\Api;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Api::class, function () {
            return new Api(
                new Client([
                    'base_uri' => config('services.delivery_holodilnik_service.uri')
                ])
            );
        });

        $this->app->bind(ConfigServiceApi::class, function () {
            return new ConfigServiceApi(
                new Client([
                    'base_uri' => config('services.config_service.uri'),
                    RequestOptions::HEADERS => [
                        'X-Config-Service-Token' => config('services.config_service.token'),
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ]
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
