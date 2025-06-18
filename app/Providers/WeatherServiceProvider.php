<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WeatherServiceFactory;
use App\Contracts\WeatherProvider;

class WeatherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WeatherServiceFactory::class);

        $this->app->bind(WeatherProvider::class, function ($app) {
            return $app->make(WeatherServiceFactory::class)->make();
        });
    }

    public function boot(): void
    {
        //
    }
} 