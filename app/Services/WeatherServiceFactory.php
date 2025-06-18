<?php

namespace App\Services;

use App\Contracts\WeatherProvider;
use InvalidArgumentException;

class WeatherServiceFactory
{
    protected $providers = [
        'openmeteo' => OpenMeteoProvider::class,
        'weatherapi' => WeatherApiProvider::class,
        'tomorrowio' => TomorrowIoWeatherProvider::class,
        'openweather' => WeatherService::class,
    ];

    public function make(string $provider = 'openmeteo'): WeatherProvider
    {
        if (!isset($this->providers[$provider])) {
            throw new InvalidArgumentException("Unsupported weather provider: {$provider}");
        }

        $providerClass = $this->providers[$provider];
        return new $providerClass();
    }

    public function getDefaultProvider(): string
    {
        return 'openmeteo'; // Open-Meteo is free and accurate for PH
    }

    public function getAllProviders(): array
    {
        return array_keys($this->providers);
    }
} 