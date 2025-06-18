<?php

namespace App\Services;

use App\Contracts\WeatherProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OpenMeteoProvider implements WeatherProvider
{
    protected $baseUrl = 'https://api.open-meteo.com/v1';
    protected $geocodingUrl = 'https://geocoding-api.open-meteo.com/v1';
    protected $marineUrl = 'https://marine-api.open-meteo.com/v1';
    protected $defaultLocation = 'Manila,Philippines';
    protected $defaultCoordinates = ['lat' => 14.5995, 'lon' => 120.9842]; // Manila

    public function getCurrentWeather(?string $location): ?array
    {
        $coordinates = $this->getCoordinates($location);
        if (!$coordinates) {
            return null;
        }

        $cacheKey = "weather_current_openmeteo_{$coordinates['lat']}_{$coordinates['lon']}";
        
        return Cache::remember($cacheKey, 1800, function () use ($coordinates) {
            $response = Http::get("{$this->baseUrl}/forecast", [
                'latitude' => $coordinates['lat'],
                'longitude' => $coordinates['lon'],
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,rain,wind_speed_10m,wind_direction_10m,wind_gusts_10m',
                'hourly' => 'temperature_2m,relative_humidity_2m,dew_point_2m,apparent_temperature,precipitation_probability,precipitation,rain,wind_speed_10m,wind_direction_10m,wind_gusts_10m,uv_index,cloud_cover',
                'timezone' => 'Asia/Manila'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatCurrentWeather($data, $coordinates);
            }

            return null;
        });
    }

    public function getForecast(?string $location): ?array
    {
        $coordinates = $this->getCoordinates($location);
        if (!$coordinates) {
            return null;
        }

        $cacheKey = "weather_forecast_openmeteo_{$coordinates['lat']}_{$coordinates['lon']}";
        
        return Cache::remember($cacheKey, 3600, function () use ($coordinates) {
            $response = Http::get("{$this->baseUrl}/forecast", [
                'latitude' => $coordinates['lat'],
                'longitude' => $coordinates['lon'],
                'daily' => 'temperature_2m_max,temperature_2m_min,apparent_temperature_max,apparent_temperature_min,precipitation_sum,precipitation_probability_max,wind_speed_10m_max,wind_gusts_10m_max,wind_direction_10m_dominant,uv_index_max',
                'hourly' => 'temperature_2m,relative_humidity_2m,precipitation_probability,precipitation,wind_speed_10m,wind_direction_10m,wind_gusts_10m,uv_index,cloud_cover',
                'timezone' => 'Asia/Manila',
                'forecast_days' => 7
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
        // Open-Meteo doesn't provide direct weather alerts
        // We'll generate alerts based on extreme weather conditions
        $weather = $this->getCurrentWeather($location);
        if (!$weather) {
            return [];
        }

        return $this->generateAlerts($weather);
    }

    public function getMarineWeather(?string $location): ?array
    {
        $coordinates = $this->getCoordinates($location);
        if (!$coordinates) {
            return null;
        }

        $cacheKey = "weather_marine_openmeteo_{$coordinates['lat']}_{$coordinates['lon']}";
        
        return Cache::remember($cacheKey, 3600, function () use ($coordinates) {
            $response = Http::get("{$this->marineUrl}/marine", [
                'latitude' => $coordinates['lat'],
                'longitude' => $coordinates['lon'],
                'hourly' => 'wave_height,wave_direction,wave_period,wind_wave_height,wind_wave_direction,wind_wave_period,swell_wave_height,swell_wave_direction,swell_wave_period',
                'timezone' => 'Asia/Manila'
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

    protected function getCoordinates(?string $location): ?array
    {
        if (empty($location) || $location === $this->defaultLocation) {
            return $this->defaultCoordinates;
        }

        $cacheKey = "geocoding_openmeteo_" . md5($location);
        
        return Cache::remember($cacheKey, 86400, function () use ($location) {
            // Remove ',PH' or ',Philippines' if present
            $searchLocation = preg_replace('/,(PH|Philippines)$/i', '', $location);
            
            $response = Http::get("{$this->geocodingUrl}/search", [
                'name' => $searchLocation,
                'count' => 1,
                'language' => 'en',
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'])) {
                    $result = $data['results'][0];
                    return [
                        'lat' => $result['latitude'],
                        'lon' => $result['longitude'],
                        'name' => $result['name'],
                        'admin1' => $result['admin1'] ?? null,
                        'country' => $result['country']
                    ];
                }
            }

            return null;
        });
    }

    protected function formatCurrentWeather(array $data, array $coordinates): array
    {
        $current = $data['current'];
        $units = $data['current_units'];

        return [
            'temperature' => $current['temperature_2m'],
            'feels_like' => $current['apparent_temperature'],
            'humidity' => $current['relative_humidity_2m'],
            'wind_speed' => $current['wind_speed_10m'] * 3.6, // Convert m/s to km/h
            'wind_direction' => $current['wind_direction_10m'],
            'wind_gusts' => $current['wind_gusts_10m'] * 3.6,
            'precipitation' => $current['precipitation'],
            'rain' => $current['rain'],
            'location' => [
                'name' => $coordinates['name'] ?? 'Unknown',
                'region' => $coordinates['admin1'] ?? 'Unknown',
                'country' => $coordinates['country'] ?? 'Philippines',
                'lat' => $coordinates['lat'],
                'lon' => $coordinates['lon'],
                'timezone' => $data['timezone'],
                'local_time' => now()->setTimezone($data['timezone'])->format('Y-m-d H:i:s')
            ],
            'units' => [
                'temperature' => $units['temperature_2m'],
                'wind_speed' => 'km/h',
                'precipitation' => $units['precipitation'],
                'humidity' => $units['relative_humidity_2m']
            ]
        ];
    }

    protected function formatForecast(array $data): array
    {
        $forecast = [];
        $daily = $data['daily'];
        $hourly = $data['hourly'];
        $timeIndex = 0;

        for ($i = 0; $i < count($daily['time']); $i++) {
            $dayForecast = [
                'date' => $daily['time'][$i],
                'max_temp' => $daily['temperature_2m_max'][$i],
                'min_temp' => $daily['temperature_2m_min'][$i],
                'max_feels_like' => $daily['apparent_temperature_max'][$i],
                'min_feels_like' => $daily['apparent_temperature_min'][$i],
                'precipitation_sum' => $daily['precipitation_sum'][$i],
                'precipitation_probability' => $daily['precipitation_probability_max'][$i],
                'max_wind_speed' => $daily['wind_speed_10m_max'][$i] * 3.6,
                'max_wind_gusts' => $daily['wind_gusts_10m_max'][$i] * 3.6,
                'wind_direction' => $daily['wind_direction_10m_dominant'][$i],
                'uv_index' => $daily['uv_index_max'][$i],
                'hourly' => []
            ];

            // Add hourly data for this day
            for ($h = 0; $h < 24; $h++) {
                if ($h % 3 === 0) { // Every 3 hours
                    $dayForecast['hourly'][] = [
                        'time' => $hourly['time'][$timeIndex],
                        'temperature' => $hourly['temperature_2m'][$timeIndex],
                        'humidity' => $hourly['relative_humidity_2m'][$timeIndex],
                        'precipitation_probability' => $hourly['precipitation_probability'][$timeIndex],
                        'precipitation' => $hourly['precipitation'][$timeIndex],
                        'wind_speed' => $hourly['wind_speed_10m'][$timeIndex] * 3.6,
                        'wind_direction' => $hourly['wind_direction_10m'][$timeIndex],
                        'wind_gusts' => $hourly['wind_gusts_10m'][$timeIndex] * 3.6,
                        'uv_index' => $hourly['uv_index'][$timeIndex],
                        'cloud_cover' => $hourly['cloud_cover'][$timeIndex]
                    ];
                }
                $timeIndex++;
            }

            $forecast[] = $dayForecast;
        }

        return $forecast;
    }

    protected function generateAlerts(array $weather): array
    {
        $alerts = [];
        
        // Check for extreme conditions
        if ($weather['temperature'] > 35) {
            $alerts[] = [
                'title' => 'Extreme Heat Warning',
                'message' => 'Temperature exceeds 35°C. Take precautions against heat exposure.',
                'severity' => 'moderate',
                'category' => 'temperature'
            ];
        }

        if ($weather['wind_speed'] > 40) {
            $alerts[] = [
                'title' => 'Strong Wind Advisory',
                'message' => 'Strong winds may affect marine activities.',
                'severity' => 'moderate',
                'category' => 'wind'
            ];
        }

        if ($weather['precipitation'] > 30) {
            $alerts[] = [
                'title' => 'Heavy Rainfall Alert',
                'message' => 'Heavy rainfall may cause flooding in low-lying areas.',
                'severity' => 'severe',
                'category' => 'precipitation'
            ];
        }

        return $alerts;
    }

    protected function formatMarineData(array $data): array
    {
        $marine = [];
        $hourly = $data['hourly'];

        for ($i = 0; $i < count($hourly['time']); $i += 3) { // Every 3 hours
            $marine[] = [
                'time' => $hourly['time'][$i],
                'wave_height' => $hourly['wave_height'][$i],
                'wave_direction' => $hourly['wave_direction'][$i],
                'wave_period' => $hourly['wave_period'][$i],
                'wind_wave_height' => $hourly['wind_wave_height'][$i],
                'wind_wave_direction' => $hourly['wind_wave_direction'][$i],
                'wind_wave_period' => $hourly['wind_wave_period'][$i],
                'swell_wave_height' => $hourly['swell_wave_height'][$i],
                'swell_wave_direction' => $hourly['swell_wave_direction'][$i],
                'swell_wave_period' => $hourly['swell_wave_period'][$i]
            ];
        }

        return $marine;
    }
} 