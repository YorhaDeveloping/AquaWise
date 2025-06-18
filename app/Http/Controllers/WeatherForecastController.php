<?php

namespace App\Http\Controllers;

use App\Services\WeatherServiceFactory;
use Illuminate\Http\Request;

class WeatherForecastController extends Controller
{
    protected $weatherProvider;

    public function __construct(WeatherServiceFactory $factory)
    {
        $this->middleware('auth');
        $this->weatherProvider = $factory->make('openmeteo');
    }

    public function index(Request $request)
    {
        try {
            // Clean and format the location
            $location = $request->query('location', 'Aparri,Philippines');
            $location = str_replace(['.', ' '], [',', ''], $location);
            if (!str_contains($location, ',')) {
                $location .= ',Philippines';
            }
            
            // Initialize default values
            $weather = null;
            $forecast = null;
            $marine = null;
            $alerts = [];
            $fishingConditions = [
                'activity_level' => 'Unknown',
                'best_time' => 'Not available',
                'recommended_spots' => [],
                'recommended_techniques' => [],
                'safety_alerts' => []
            ];

            // Fetch weather data
            $weather = $this->weatherProvider->getCurrentWeather($location);
            
            // Only proceed with additional data if we have basic weather info
            if ($weather) {
                $forecast = $this->weatherProvider->getForecast($location);
                $marine = $this->weatherProvider->getMarineWeather($location);
                $fishingConditions = $this->analyzeFishingConditions($weather, $marine);
            }

            return view('weather.index', compact(
                'weather',
                'forecast',
                'marine',
                'alerts',
                'fishingConditions',
                'location'
            ));
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Weather data fetch error: ' . $e->getMessage());
            
            // Return to the view with error message and default values
            return view('weather.index', compact(
                'weather',
                'forecast',
                'marine',
                'alerts',
                'fishingConditions',
                'location'
            ))->with('error', 'Unable to fetch weather data. Please check the location name and try again.');
        }
    }

    public function getWeatherData(Request $request)
    {
        // Clean and format the location
        $location = $request->query('location', 'Aparri,Philippines');
        $location = str_replace(['.', ' '], [',', ''], $location);
        if (!str_contains($location, ',')) {
            $location .= ',Philippines';
        }
        
        try {
            $weather = $this->weatherProvider->getCurrentWeather($location);
            
            if (!$weather) {
                throw new \Exception('Weather data not available');
            }
            
            $marine = $this->weatherProvider->getMarineWeather($location);
            $fishingConditions = $this->analyzeFishingConditions($weather, $marine);

            return response()->json([
                'weather' => $weather,
                'marine' => $marine,
                'conditions' => $fishingConditions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch weather data. Please check the location name and try again.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function fetch(Request $request)
    {
        try {
            $latitude = $request->query('latitude');
            $longitude = $request->query('longitude');
            $location = $request->query('location');
            
            // Log the request for debugging
            \Log::info('Weather fetch request', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location' => $location
            ]);

            // Initialize variables
            $weather = null;
            $forecast = null;
            $marine = null;
            $alerts = [];

            // Determine whether to use coordinates or location
            if (!empty($latitude) && !empty($longitude)) {
                // Validate coordinates
                $request->validate([
                    'latitude' => 'required|numeric|between:-90,90',
                    'longitude' => 'required|numeric|between:-180,180',
                ]);
                
                try {
                    $weather = $this->weatherProvider->getCurrentWeatherByCoordinates($latitude, $longitude);
                    $forecast = $this->weatherProvider->getForecastByCoordinates($latitude, $longitude);
                    $marine = $this->weatherProvider->getMarineWeatherByCoordinates($latitude, $longitude);
                } catch (\Exception $e) {
                    \Log::error('Coordinate-based weather fetch failed', [
                        'error' => $e->getMessage(),
                        'lat' => $latitude,
                        'lon' => $longitude
                    ]);
                    throw new \Exception('Unable to fetch weather data for the specified coordinates');
                }
            } else {
                // Use location-based query
                $location = $location ?: 'Aparri,Philippines';
                $location = str_replace(['.', ' '], [',', ''], $location);
                if (!str_contains($location, ',')) {
                    $location .= ',Philippines';
                }
                
                try {
                    $weather = $this->weatherProvider->getCurrentWeather($location);
                    $forecast = $this->weatherProvider->getForecast($location);
                    $marine = $this->weatherProvider->getMarineWeather($location);
                } catch (\Exception $e) {
                    \Log::error('Location-based weather fetch failed', [
                        'error' => $e->getMessage(),
                        'location' => $location
                    ]);
                    throw new \Exception('Unable to fetch weather data for ' . $location);
                }
            }
            
            if (!$weather) {
                throw new \Exception('Failed to fetch weather data');
            }

            // Calculate fishing conditions
            $fishingConditions = $this->analyzeFishingConditions($weather, $marine);

            // Return the weather component view
            return view('components.weather-forecast', compact(
                'weather',
                'forecast',
                'marine',
                'alerts',
                'fishingConditions'
            ))->render();

        } catch (\Exception $e) {
            // Log the detailed error
            \Log::error('Weather data fetch error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            // Return a more informative error response
            return response()->view('components.error-message', [
                'message' => $e->getMessage()
            ], 422);
        }
    }

    protected function analyzeFishingConditions($weather, $marine = null)
    {
        if (!$weather) {
            return [
                'activity_level' => 'Unknown',
                'best_time' => 'Data unavailable',
                'recommended_spots' => [],
                'recommended_techniques' => [],
                'safety_alerts' => ['Weather data is currently unavailable.']
            ];
        }

        $temp = $weather['main']['temp'] ?? null;
        $windSpeed = $weather['wind']['speed'] ?? null;
        $clouds = $weather['clouds']['all'] ?? null;
        $weatherMain = $weather['weather'][0]['main'] ?? null;
        $humidity = $weather['main']['humidity'] ?? null;

        // Convert wind speed from m/s to km/h
        $windSpeedKmh = $windSpeed ? ($windSpeed * 3.6) : null;

        $conditions = [
            'activity_level' => $this->calculateActivityLevel($temp, $windSpeedKmh, $clouds, $humidity),
            'best_time' => $this->determineBestFishingTime($weatherMain, $temp),
            'recommended_spots' => $this->recommendFishingSpots($weatherMain, $windSpeedKmh),
            'recommended_techniques' => $this->recommendTechniques($weatherMain, $windSpeedKmh),
            'safety_alerts' => $this->generateSafetyAlerts($weatherMain, $windSpeedKmh),
            'local_conditions' => [
                'temperature' => $temp ? round($temp, 1) . '°C' : 'N/A',
                'wind_speed' => $windSpeedKmh ? round($windSpeedKmh, 1) . ' km/h' : 'N/A',
                'humidity' => $humidity ? $humidity . '%' : 'N/A',
                'cloud_cover' => $clouds ? $clouds . '%' : 'N/A',
                'weather' => $weatherMain ?? 'N/A',
                'local_time' => now()->setTimezone('Asia/Manila')->format('h:i A')
            ]
        ];

        return $conditions;
    }

    protected function calculateActivityLevel($temp, $windSpeedKmh, $clouds, $humidity)
    {
        if (!$temp || !$windSpeedKmh || !$clouds || !$humidity) {
            return 'Unknown';
        }

        $score = 0;

        // Temperature scoring (ideal for PH: 26-30°C)
        if ($temp >= 26 && $temp <= 30) $score += 3;
        elseif ($temp >= 24 && $temp <= 32) $score += 2;
        else $score += 1;

        // Wind speed scoring (ideal: 5-15 km/h)
        if ($windSpeedKmh >= 5 && $windSpeedKmh <= 15) $score += 3;
        elseif ($windSpeedKmh < 20) $score += 2;
        else $score += 1;

        // Cloud cover scoring (ideal: 30-70%)
        if ($clouds >= 30 && $clouds <= 70) $score += 3;
        elseif ($clouds < 90) $score += 2;
        else $score += 1;

        // Humidity scoring (ideal: 60-80%)
        if ($humidity >= 60 && $humidity <= 80) $score += 3;
        elseif ($humidity >= 50 && $humidity <= 90) $score += 2;
        else $score += 1;

        if ($score >= 10) {
            return 'High';
        } elseif ($score >= 7) {
            return 'Moderate';
        } else {
            return 'Low';
        }
    }

    protected function determineBestFishingTime($weatherMain, $temp)
    {
        // Favorable times: early morning or late afternoon, especially if not raining and temp is not too hot
        if (in_array(strtolower($weatherMain), ['rain', 'thunderstorm'])) {
            return 'Avoid fishing during rain or thunderstorms';
        }
        if ($temp !== null && $temp > 32) {
            return 'Early morning (before 9am) or late afternoon (after 4pm)';
        }
        return 'Early morning or late afternoon';
    }

    protected function recommendFishingSpots($weatherMain, $windSpeedKmh)
    {
        $spots = [];
        if (in_array(strtolower($weatherMain), ['clear', 'clouds'])) {
            $spots[] = 'Shallow coastal areas';
            $spots[] = 'Near river mouths';
        }
        if ($windSpeedKmh !== null && $windSpeedKmh > 15) {
            $spots[] = 'Sheltered bays or coves';
        }
        if (empty($spots)) {
            $spots[] = 'Try piers or breakwaters';
        }
        return $spots;
    }

    protected function recommendTechniques($weatherMain, $windSpeedKmh)
    {
        $techniques = [];
        $main = strtolower($weatherMain);
        if ($main === 'clear') {
            $techniques[] = 'Surface lures';
            $techniques[] = 'Live bait fishing';
        } elseif ($main === 'clouds') {
            $techniques[] = 'Jigging';
            $techniques[] = 'Bottom fishing';
        } elseif ($main === 'rain') {
            $techniques[] = 'Use bright-colored lures';
        } else {
            $techniques[] = 'Try versatile rigs (e.g., Carolina rig)';
        }
        if ($windSpeedKmh !== null && $windSpeedKmh > 15) {
            $techniques[] = 'Heavier sinkers for strong wind';
        }
        return $techniques;
    }

    protected function generateSafetyAlerts($weatherMain, $windSpeedKmh)
    {
        $alerts = [];
        $main = strtolower($weatherMain);
        if (in_array($main, ['thunderstorm', 'rain'])) {
            $alerts[] = 'Thunderstorms or heavy rain: Avoid fishing and seek shelter.';
        }
        if ($windSpeedKmh !== null && $windSpeedKmh > 25) {
            $alerts[] = 'Strong winds: Exercise caution, especially on open water.';
        }
        if (empty($alerts)) {
            return [];
        }
        return $alerts;
    }
} 