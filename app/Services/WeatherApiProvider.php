<?php

namespace App\Services;

use App\Contracts\WeatherProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WeatherApiProvider implements WeatherProvider
{
    protected $apiKey;
    protected $baseUrl = 'https://api.weatherapi.com/v1';
    protected $defaultLocation = 'Manila,Philippines';

    public function __construct()
    {
        $this->apiKey = config('services.weatherapi.key');
    }

    public function getCurrentWeather(?string $location): ?array
    {
        $location = $this->formatLocation($location);
        $cacheKey = "weather_current_weatherapi_{$location}";
        
        return Cache::remember($cacheKey, 1800, function () use ($location) {
            $response = Http::get("{$this->baseUrl}/current.json", [
                'key' => $this->apiKey,
                'q' => $location,
                'aqi' => 'yes' // Include air quality data
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatCurrentWeather($data);
            }

            return null;
        });
    }

    public function getForecast(?string $location): ?array
    {
        $location = $this->formatLocation($location);
        $cacheKey = "weather_forecast_weatherapi_{$location}";
        
        return Cache::remember($cacheKey, 3600, function () use ($location) {
            $response = Http::get("{$this->baseUrl}/forecast.json", [
                'key' => $this->apiKey,
                'q' => $location,
                'days' => 3,
                'aqi' => 'yes',
                'alerts' => 'yes'
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
        $location = $this->formatLocation($location);
        $cacheKey = "weather_alerts_weatherapi_{$location}";
        
        return Cache::remember($cacheKey, 1800, function () use ($location) {
            $response = Http::get("{$this->baseUrl}/forecast.json", [
                'key' => $this->apiKey,
                'q' => $location,
                'days' => 1,
                'alerts' => 'yes'
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
        $location = $this->formatLocation($location);
        $cacheKey = "weather_marine_weatherapi_{$location}";
        
        return Cache::remember($cacheKey, 3600, function () use ($location) {
            $response = Http::get("{$this->baseUrl}/marine.json", [
                'key' => $this->apiKey,
                'q' => $location,
                'days' => 1
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
        try {
            $response = Http::get("{$this->baseUrl}/search.json", [
                'key' => $this->apiKey,
                'q' => $location
            ]);

            return $response->successful() && !empty($response->json());
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function formatLocation(?string $location): string
    {
        if (empty($location)) {
            return $this->defaultLocation;
        }

        // Convert "City,PH" format to "City,Philippines"
        if (Str::endsWith($location, ',PH')) {
            $city = Str::before($location, ',PH');
            return $city . ',Philippines';
        }

        return $location;
    }

    protected function formatCurrentWeather(array $data): array
    {
        $current = $data['current'];
        $location = $data['location'];

        return [
            'temperature' => $current['temp_c'],
            'feels_like' => $current['feelslike_c'],
            'humidity' => $current['humidity'],
            'wind_speed' => $current['wind_kph'],
            'wind_direction' => $current['wind_dir'],
            'wind_degree' => $current['wind_degree'],
            'pressure' => $current['pressure_mb'],
            'precipitation' => $current['precip_mm'],
            'cloud_cover' => $current['cloud'],
            'uv_index' => $current['uv'],
            'air_quality' => [
                'pm2_5' => $current['air_quality']['pm2_5'] ?? null,
                'pm10' => $current['air_quality']['pm10'] ?? null,
                'co' => $current['air_quality']['co'] ?? null,
                'no2' => $current['air_quality']['no2'] ?? null,
                'o3' => $current['air_quality']['o3'] ?? null,
                'so2' => $current['air_quality']['so2'] ?? null,
            ],
            'condition' => [
                'text' => $current['condition']['text'],
                'icon' => $current['condition']['icon'],
                'code' => $current['condition']['code']
            ],
            'location' => [
                'name' => $location['name'],
                'region' => $location['region'],
                'country' => $location['country'],
                'lat' => $location['lat'],
                'lon' => $location['lon'],
                'timezone' => $location['tz_id'],
                'local_time' => $location['localtime']
            ],
            'last_updated' => $current['last_updated']
        ];
    }

    protected function formatForecast(array $data): array
    {
        $forecast = [];
        foreach ($data['forecast']['forecastday'] as $day) {
            $forecast[] = [
                'date' => $day['date'],
                'max_temp' => $day['day']['maxtemp_c'],
                'min_temp' => $day['day']['mintemp_c'],
                'avg_temp' => $day['day']['avgtemp_c'],
                'max_wind_kph' => $day['day']['maxwind_kph'],
                'total_precipitation' => $day['day']['totalprecip_mm'],
                'avg_humidity' => $day['day']['avghumidity'],
                'chance_of_rain' => $day['day']['daily_chance_of_rain'],
                'chance_of_snow' => $day['day']['daily_chance_of_snow'],
                'condition' => [
                    'text' => $day['day']['condition']['text'],
                    'icon' => $day['day']['condition']['icon'],
                    'code' => $day['day']['condition']['code']
                ],
                'astro' => [
                    'sunrise' => $day['astro']['sunrise'],
                    'sunset' => $day['astro']['sunset'],
                    'moonrise' => $day['astro']['moonrise'],
                    'moonset' => $day['astro']['moonset'],
                    'moon_phase' => $day['astro']['moon_phase'],
                    'moon_illumination' => $day['astro']['moon_illumination']
                ],
                'hourly' => $this->formatHourlyForecast($day['hour'])
            ];
        }

        return $forecast;
    }

    protected function formatHourlyForecast(array $hours): array
    {
        $hourly = [];
        foreach ($hours as $hour) {
            $time = strtotime($hour['time']);
            $localTime = now()->setTimestamp($time)->setTimezone('Asia/Manila');
            
            // Only include specific hours (e.g., every 3 hours)
            if ($localTime->format('H') % 3 === 0) {
                $hourly[] = [
                    'time' => $hour['time'],
                    'temp_c' => $hour['temp_c'],
                    'wind_kph' => $hour['wind_kph'],
                    'wind_dir' => $hour['wind_dir'],
                    'pressure' => $hour['pressure_mb'],
                    'precipitation' => $hour['precip_mm'],
                    'humidity' => $hour['humidity'],
                    'cloud_cover' => $hour['cloud'],
                    'feels_like' => $hour['feelslike_c'],
                    'chance_of_rain' => $hour['chance_of_rain'],
                    'condition' => [
                        'text' => $hour['condition']['text'],
                        'icon' => $hour['condition']['icon'],
                        'code' => $hour['condition']['code']
                    ]
                ];
            }
        }
        return $hourly;
    }

    protected function formatAlerts(array $data): array
    {
        $alerts = [];
        foreach ($data['alerts']['alert'] ?? [] as $alert) {
            $alerts[] = [
                'headline' => $alert['headline'],
                'message' => $alert['desc'],
                'severity' => $alert['severity'],
                'urgency' => $alert['urgency'],
                'areas' => $alert['areas'],
                'category' => $alert['category'],
                'effective' => $alert['effective'],
                'expires' => $alert['expires'],
                'instruction' => $alert['instruction'] ?? null
            ];
        }
        return $alerts;
    }

    protected function formatMarineData(array $data): array
    {
        $marine = [];
        foreach ($data['forecast']['forecastday'] as $day) {
            foreach ($day['hour'] as $hour) {
                $time = strtotime($hour['time']);
                $localTime = now()->setTimestamp($time)->setTimezone('Asia/Manila');
                
                if ($localTime->format('H') % 3 === 0) { // Every 3 hours
                    $marine[] = [
                        'time' => $hour['time'],
                        'significant_wave_height' => $hour['significant_wave_height'],
                        'swell_wave_height' => $hour['swell_wave_height'],
                        'swell_wave_direction' => $hour['swell_wave_direction'],
                        'swell_wave_period' => $hour['swell_wave_period'],
                        'wind_wave_height' => $hour['wind_wave_height'],
                        'wind_wave_direction' => $hour['wind_wave_direction'],
                        'wind_wave_period' => $hour['wind_wave_period'],
                        'wind_speed_kph' => $hour['wind_kph'],
                        'wind_direction' => $hour['wind_dir'],
                        'water_temperature' => $hour['water_temp_c']
                    ];
                }
            }
        }
        return $marine;
    }
} 