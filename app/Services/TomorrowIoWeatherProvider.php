<?php

namespace App\Services;

use App\Contracts\WeatherProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TomorrowIoWeatherProvider implements WeatherProvider
{
    protected $apiKey;
    protected $baseUrl = 'https://api.tomorrow.io/v4';
    protected $defaultLocation = 'Manila,PH';
    protected $philippineRegions;

    public function __construct()
    {
        $this->apiKey = config('services.tomorrowio.key');
        $this->philippineRegions = config('locations.philippines');
    }

    public function getCurrentWeather(?string $location): ?array
    {
        $location = $this->validateAndFormatLocation($location);
        $coordinates = $this->getCoordinates($location);
        
        if (!$coordinates) {
            return null;
        }

        $cacheKey = "weather_current_tomorrow_{$location}";
        
        return Cache::remember($cacheKey, 1800, function () use ($coordinates) {
            $response = Http::get("{$this->baseUrl}/weather/realtime", [
                'location' => "{$coordinates['lat']},{$coordinates['lon']}",
                'apikey' => $this->apiKey,
                'units' => 'metric',
                'fields' => 'temperature,humidity,windSpeed,windDirection,weatherCode,pressureSurfaceLevel,precipitationProbability,cloudCover'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $data['local_time'] = now()->setTimezone('Asia/Manila')->format('h:i A');
                return $this->formatCurrentWeather($data);
            }

            return null;
        });
    }

    public function getForecast(?string $location): ?array
    {
        $location = $this->validateAndFormatLocation($location);
        $coordinates = $this->getCoordinates($location);
        
        if (!$coordinates) {
            return null;
        }

        $cacheKey = "weather_forecast_tomorrow_{$location}";
        
        return Cache::remember($cacheKey, 3600, function () use ($coordinates) {
            $response = Http::get("{$this->baseUrl}/weather/forecast", [
                'location' => "{$coordinates['lat']},{$coordinates['lon']}",
                'apikey' => $this->apiKey,
                'units' => 'metric',
                'timesteps' => '1h',
                'fields' => 'temperature,humidity,windSpeed,windDirection,weatherCode,pressureSurfaceLevel,precipitationProbability,cloudCover'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatForecast($data);
            }

            return null;
        });
    }

    public function getAlerts(?string $location): array
    {
        $location = $this->validateAndFormatLocation($location);
        $coordinates = $this->getCoordinates($location);
        
        if (!$coordinates) {
            return [];
        }

        $cacheKey = "weather_alerts_tomorrow_{$location}";
        
        return Cache::remember($cacheKey, 1800, function () use ($coordinates) {
            $response = Http::get("{$this->baseUrl}/weather/alerts", [
                'location' => "{$coordinates['lat']},{$coordinates['lon']}",
                'apikey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatAlerts($data);
            }

            return [];
        });
    }

    public function getMarineWeather(?string $location): ?array
    {
        $location = $this->validateAndFormatLocation($location);
        $coordinates = $this->getCoordinates($location);
        
        if (!$coordinates) {
            return null;
        }

        $cacheKey = "weather_marine_tomorrow_{$location}";
        
        return Cache::remember($cacheKey, 3600, function () use ($coordinates) {
            $response = Http::get("{$this->baseUrl}/weather/forecast", [
                'location' => "{$coordinates['lat']},{$coordinates['lon']}",
                'apikey' => $this->apiKey,
                'units' => 'metric',
                'timesteps' => '1h',
                'fields' => 'waveHeight,waveDirection,wavePeriod,windWaveHeight,swellHeight,swellDirection,swellPeriod,secondarySwellHeight,secondarySwellDirection,secondarySwellPeriod'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatMarineData($data);
            }

            return null;
        });
    }

    public function supportsLocation(string $location): bool
    {
        return $this->getCoordinates($location) !== null;
    }

    protected function validateAndFormatLocation(?string $location): string
    {
        if (empty($location)) {
            return $this->defaultLocation;
        }

        // Remove ',PH' if it exists to check the city name
        $cityName = Str::before($location, ',');
        
        // Check if the city exists in our Philippine regions
        $found = false;
        foreach ($this->philippineRegions as $cities) {
            if (in_array($cityName, $cities)) {
                $found = true;
                break;
            }
        }

        // If not found, return default location
        if (!$found) {
            return $this->defaultLocation;
        }

        // Ensure location ends with ',PH'
        return Str::endsWith($location, ',PH') ? $location : $location . ',PH';
    }

    protected function getCoordinates(string $location): ?array
    {
        // This would typically use a geocoding service or a local database
        // For now, we'll use a simple mapping for demonstration
        $coordinates = [
            'Manila,PH' => ['lat' => 14.5995, 'lon' => 120.9842],
            'Cebu City,PH' => ['lat' => 10.3157, 'lon' => 123.8854],
            'Davao City,PH' => ['lat' => 7.0707, 'lon' => 125.6087],
            // Add more Philippine cities as needed
        ];

        return $coordinates[$location] ?? null;
    }

    protected function formatCurrentWeather(array $data): array
    {
        return [
            'temperature' => $data['data']['values']['temperature'],
            'humidity' => $data['data']['values']['humidity'],
            'wind_speed' => $data['data']['values']['windSpeed'],
            'wind_direction' => $data['data']['values']['windDirection'],
            'weather_code' => $this->mapWeatherCode($data['data']['values']['weatherCode']),
            'cloud_cover' => $data['data']['values']['cloudCover'],
            'precipitation_probability' => $data['data']['values']['precipitationProbability'],
            'pressure' => $data['data']['values']['pressureSurfaceLevel'],
            'local_time' => $data['local_time']
        ];
    }

    protected function formatForecast(array $data): array
    {
        $formattedData = [];
        foreach ($data['timelines']['hourly'] as $hourly) {
            $time = strtotime($hourly['time']);
            $localTime = now()->setTimestamp($time)->setTimezone('Asia/Manila');
            
            // Only include 12 PM and 3 PM forecasts
            if (in_array($localTime->format('H'), ['12', '15'])) {
                $formattedData[] = [
                    'time' => $localTime->format('Y-m-d H:i:s'),
                    'temperature' => $hourly['values']['temperature'],
                    'humidity' => $hourly['values']['humidity'],
                    'wind_speed' => $hourly['values']['windSpeed'],
                    'wind_direction' => $hourly['values']['windDirection'],
                    'weather_code' => $this->mapWeatherCode($hourly['values']['weatherCode']),
                    'cloud_cover' => $hourly['values']['cloudCover'],
                    'precipitation_probability' => $hourly['values']['precipitationProbability']
                ];
            }
        }
        return $formattedData;
    }

    protected function formatAlerts(array $data): array
    {
        $alerts = [];
        foreach ($data['alerts'] ?? [] as $alert) {
            $alerts[] = [
                'title' => $alert['title'],
                'description' => $alert['description'],
                'severity' => $alert['severity'],
                'start_time' => $alert['start'],
                'end_time' => $alert['end']
            ];
        }
        return $alerts;
    }

    protected function formatMarineData(array $data): array
    {
        $marineData = [];
        foreach ($data['timelines']['hourly'] as $hourly) {
            $time = strtotime($hourly['time']);
            $localTime = now()->setTimestamp($time)->setTimezone('Asia/Manila');
            
            $marineData[] = [
                'time' => $localTime->format('Y-m-d H:i:s'),
                'wave_height' => $hourly['values']['waveHeight'],
                'wave_direction' => $hourly['values']['waveDirection'],
                'wave_period' => $hourly['values']['wavePeriod'],
                'wind_wave_height' => $hourly['values']['windWaveHeight'],
                'swell_height' => $hourly['values']['swellHeight'],
                'swell_direction' => $hourly['values']['swellDirection'],
                'swell_period' => $hourly['values']['swellPeriod']
            ];
        }
        return $marineData;
    }

    protected function mapWeatherCode(int $code): string
    {
        // Tomorrow.io weather codes mapping to general weather conditions
        return match($code) {
            1000 => 'Clear',
            1100, 1101, 1102 => 'Clouds',
            4000, 4001, 4200, 4201 => 'Rain',
            8000 => 'Thunderstorm',
            default => 'Unknown'
        };
    }
} 