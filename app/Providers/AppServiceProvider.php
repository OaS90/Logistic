<?php

namespace App\Providers;

use App\Infrastructure\Services\Delivery\Api\Api;
use GuzzleHttp\Client;
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
    public function register()
    {
        $this->app->bind(Api::class, function () {
            return new Api(
                new Client([
                    'base_uri' => config('services.delivery_service.uri')
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
