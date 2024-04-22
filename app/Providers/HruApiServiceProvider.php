<?php

namespace App\Providers;

use App\Infrastructure\Admin\Services\Api\HruApi;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\ServiceProvider;

class HruApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(HruApi::class, function () {
            return new HruApi(
                new Client([
                    'base_uri' => config('app.tk_hru_url'),
                    RequestOptions::HEADERS =>  [
                        'Content-Type' => 'application/json', 'Accept' => 'application/json',
                        'Authorization' => config('app.api_hru_token')
                    ],
                ])
            );
        });
    }
}
