<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WeatherService
{
    protected $baseUrl = 'https://api.open-meteo.com/v1';
    protected $geocodingUrl = 'https://geocoding-api.open-meteo.com/v1';
    protected $defaultLocation = 'Manila,Philippines';
    protected $philippineRegions = [
        'NCR' => ['Manila', 'Quezon City', 'Makati', 'Taguig', 'Pasig'],
        'Region I' => ['Laoag', 'San Fernando', 'Vigan'],
        'Region II' => ['Tuguegarao', 'Ilagan', 'Cauayan'],
        'Region III' => ['San Fernando', 'Angeles', 'Olongapo'],
        'Region IV-A' => ['Calamba', 'Batangas City', 'Lucena'],
        'Region IV-B' => ['Calapan', 'Puerto Princesa'],
        'Region V' => ['Legazpi', 'Naga', 'Sorsogon'],
        'Region VI' => ['Iloilo City', 'Bacolod', 'Roxas'],
        'Region VII' => ['Cebu City', 'Mandaue', 'Lapu-Lapu'],
        'Region VIII' => ['Tacloban', 'Ormoc', 'Calbayog'],
        'Region IX' => ['Zamboanga City', 'Dipolog', 'Pagadian'],
        'Region X' => ['Cagayan de Oro', 'Iligan', 'Valencia'],
        'Region XI' => ['Davao City', 'Digos', 'Tagum'],
        'Region XII' => ['General Santos', 'Koronadal', 'Kidapawan'],
        'Region XIII' => ['Butuan', 'Surigao City', 'Bislig'],
        'CAR' => ['Baguio', 'Tabuk', 'Bontoc'],
        'BARMM' => ['Cotabato City', 'Marawi', 'Lamitan']
    ];

    public function __construct()
    {
        // No API key needed for Open-Meteo
    }

    public function getCurrentWeather($location = null)
    {
        try {
            $location = $this->validateAndFormatLocation($location);
            $cacheKey = "weather_current_{$location}";
            
            return Cache::remember($cacheKey, 1800, function () use ($location) {
                // Get coordinates from location
                $coordinates = $this->getCoordinates($location);
                if (!$coordinates) {
                    \Log::error('Failed to get coordinates for location', ['location' => $location]);
                    return null;
                }

                $response = Http::get("{$this->baseUrl}/forecast", [
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                    'current_weather' => true,
                    'hourly' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m,wind_direction_10m,cloud_cover',
                    'timezone' => 'Asia/Manila'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Get the current hour index
                    $currentTime = now()->setTimezone('Asia/Manila');
                    $startTime = \Carbon\Carbon::parse($data['hourly']['time'][0]);
                    $hourIndex = $currentTime->diffInHours($startTime);
                    
                    // Transform Open-Meteo format to our standard format
                    return [
                        'main' => [
                            'temp' => $data['current_weather']['temperature'],
                            'humidity' => $data['hourly']['relative_humidity_2m'][$hourIndex] ?? 0
                        ],
                        'wind' => [
                            'speed' => $data['current_weather']['windspeed'],
                            'deg' => $data['current_weather']['winddirection']
                        ],
                        'clouds' => [
                            'all' => $data['hourly']['cloud_cover'][$hourIndex] ?? 0
                        ],
                        'weather' => [
                            [
                                'main' => $this->getWeatherDescription($data['current_weather']['weathercode']),
                                'description' => $this->getWeatherDescription($data['current_weather']['weathercode'])
                            ]
                        ],
                        'local_time' => $currentTime->format('h:i A'),
                        'location' => $location
                    ];
                }

                \Log::error('Weather API error', [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);

                return null;
            });
        } catch (\Exception $e) {
            \Log::error('Weather fetch error', [
                'message' => $e->getMessage(),
                'location' => $location
            ]);
            return null;
        }
    }

    public function getForecast($location = null)
    {
        try {
            $location = $this->validateAndFormatLocation($location);
            $cacheKey = "weather_forecast_{$location}";
            
            return Cache::remember($cacheKey, 3600, function () use ($location) {
                // Get coordinates from location
                $coordinates = $this->getCoordinates($location);
                if (!$coordinates) {
                    return null;
                }

                $response = Http::get("{$this->baseUrl}/forecast", [
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                    'daily' => 'temperature_2m_max,temperature_2m_min,weathercode,wind_speed_10m_max',
                    'timezone' => 'Asia/Manila'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $forecast = [];
                    
                    // Transform the daily forecast data
                    for ($i = 0; $i < count($data['daily']['time']); $i++) {
                        $forecast[] = [
                            'dt' => strtotime($data['daily']['time'][$i]),
                            'main' => [
                                'temp' => ($data['daily']['temperature_2m_max'][$i] + $data['daily']['temperature_2m_min'][$i]) / 2,
                                'temp_min' => $data['daily']['temperature_2m_min'][$i],
                                'temp_max' => $data['daily']['temperature_2m_max'][$i]
                            ],
                            'wind' => [
                                'speed' => $data['daily']['wind_speed_10m_max'][$i]
                            ],
                            'weather' => [
                                [
                                    'main' => $this->getWeatherDescription($data['daily']['weathercode'][$i]),
                                    'description' => $this->getWeatherDescription($data['daily']['weathercode'][$i])
                                ]
                            ]
                        ];
                    }

                    return ['list' => $forecast];
                }

                return null;
            });
        } catch (\Exception $e) {
            \Log::error('Forecast fetch error', [
                'message' => $e->getMessage(),
                'location' => $location
            ]);
            return null;
        }
    }

    public function getPhilippineLocations()
    {
        $locations = [];
        foreach ($this->philippineRegions as $region => $cities) {
            $locations[$region] = array_map(function($city) {
                return $city . ',PH';
            }, $cities);
        }
        return $locations;
    }

    protected function validateAndFormatLocation($location)
    {
        if (empty($location)) {
            return $this->defaultLocation;
        }

        // Clean up the location string
        $location = str_replace(['.', ' '], [',', ''], $location);
        
        // Ensure location ends with Philippines
        if (!str_contains(strtolower($location), 'philippines') && !str_contains($location, ',ph')) {
            $location .= ',Philippines';
        }

        return $location;
    }

    public function analyzeFishingConditions($weatherData)
    {
        if (!$weatherData) {
            return [
                'activity_level' => 'Unknown',
                'best_time' => 'Data unavailable',
                'recommended_spots' => [],
                'recommended_techniques' => [],
                'safety_alerts' => ['Weather data is currently unavailable.']
            ];
        }

        $temp = $weatherData['main']['temp'] ?? null;
        $windSpeed = $weatherData['wind']['speed'] ?? null;
        $clouds = $weatherData['clouds']['all'] ?? null;
        $weatherMain = $weatherData['weather'][0]['main'] ?? null;
        $humidity = $weatherData['main']['humidity'] ?? null;

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

    protected function calculateActivityLevel($temp, $windSpeed, $clouds, $humidity)
    {
        if (!$temp || !$windSpeed || !$clouds || !$humidity) {
            return 'Unknown';
        }

        // Scoring system adjusted for Philippine climate
        $score = 0;

        // Temperature scoring (adjusted for tropical climate)
        if ($temp >= 26 && $temp <= 30) $score += 3; // Ideal tropical temperature
        elseif ($temp >= 24 && $temp <= 32) $score += 2;
        else $score += 1;

        // Wind speed scoring
        if ($windSpeed >= 5 && $windSpeed <= 15) $score += 3;
        elseif ($windSpeed < 20) $score += 2;
        else $score += 1;

        // Cloud cover scoring
        if ($clouds >= 30 && $clouds <= 70) $score += 3;
        elseif ($clouds < 90) $score += 2;
        else $score += 1;

        // Humidity scoring (important in tropical climate)
        if ($humidity >= 60 && $humidity <= 80) $score += 3;
        elseif ($humidity >= 50 && $humidity <= 90) $score += 2;
        else $score += 1;

        return match(true) {
            $score >= 10 => 'High',
            $score >= 7 => 'Moderate',
            default => 'Low'
        };
    }

    protected function determineBestFishingTime($weatherMain, $temp)
    {
        if (!$weatherMain || !$temp) {
            return 'Data unavailable';
        }

        // Adjusted for Philippine climate
        return match($weatherMain) {
            'Clear' => $temp > 30 ? 'Early Morning (4AM-7AM) or Late Afternoon (4PM-6PM)' : 'Early Morning to Mid-Morning',
            'Clouds' => 'Throughout the Day',
            'Rain' => 'Before or After Rain Showers',
            'Thunderstorm' => 'Not Recommended - Safety Risk',
            default => 'Early Morning or Late Afternoon'
        };
    }

    protected function recommendFishingSpots($weatherMain, $windSpeed)
    {
        $spots = [];

        if (!$weatherMain || !$windSpeed) {
            return ['Data unavailable'];
        }

        // Base recommendations on weather conditions
        $weatherSpots = [
            'Clear' => [
                'Coral reef areas during early morning or late afternoon',
                'Deep water drop-offs for pelagic fish',
                'Seagrass beds during cooler hours',
                'Artificial reefs and FADs (Fish Aggregating Devices)',
            ],
            'Clouds' => [
                'Mangrove channels and estuaries',
                'Rocky coastal areas',
                'Near payaos (traditional FADs)',
                'Reef edges and coral outcrops',
            ],
            'Rain' => [
                'River mouths and deltas',
                'Protected bays and coves',
                'Near breakwaters and seawalls',
                'Sheltered portions of mangrove areas',
            ],
            'Drizzle' => [
                'Inshore reefs',
                'Protected lagoons',
                'Near port structures',
                'Shallow coral areas',
            ],
            'Thunderstorm' => [
                'Fishing not recommended - Safety hazard',
            ]
        ];

        // Add weather-specific spots
        if (isset($weatherSpots[$weatherMain])) {
            $spots = array_merge($spots, $weatherSpots[$weatherMain]);
        }

        // Add wind-specific recommendations
        if ($windSpeed < 10) {
            $spots[] = 'Offshore reef drops';
            $spots[] = 'Open water fishing grounds';
            $spots[] = 'Seamounts and underwater structures';
        } elseif ($windSpeed < 20) {
            $spots[] = 'Mid-water reef areas';
            $spots[] = 'Protected outer reef sections';
            $spots[] = 'Leeward side of islands';
        } else {
            $spots[] = 'Sheltered inshore areas';
            $spots[] = 'Protected coves and bays';
            $spots[] = 'Inner reef lagoons';
        }

        return array_unique($spots);
    }

    protected function recommendTechniques($weatherMain, $windSpeed)
    {
        $techniques = [];

        if (!$weatherMain || !$windSpeed) {
            return ['Data unavailable'];
        }

        // Base techniques on weather conditions
        $weatherTechniques = [
            'Clear' => [
                'Bottom fishing with live bait (tambang, dulong)',
                'Trolling with artificial lures during early morning',
                'Jigging for pelagic species',
                'Surface popping for predatory fish',
                'Cast netting in shallow waters',
            ],
            'Clouds' => [
                'Drift fishing with natural baits',
                'Slow jigging techniques',
                'Deep dropping for bottom fish',
                'Live bait fishing near structures',
                'Vertical jigging at different depths',
            ],
            'Rain' => [
                'Use bright-colored lures or baits',
                'Bottom fishing with natural baits',
                'Slow retrieval techniques',
                'Using scented baits or attractants',
                'Bait fishing near structure edges',
            ],
            'Drizzle' => [
                'Mid-water fishing techniques',
                'Slow trolling with live bait',
                'Drift fishing with cut bait',
                'Jigging near reef edges',
            ],
            'Thunderstorm' => [
                'Fishing not recommended - Safety hazard',
            ]
        ];

        // Add weather-specific techniques
        if (isset($weatherTechniques[$weatherMain])) {
            $techniques = array_merge($techniques, $weatherTechniques[$weatherMain]);
        }

        // Add wind-specific recommendations
        if ($windSpeed < 10) {
            $techniques[] = 'Surface lure fishing';
            $techniques[] = 'Light tackle techniques';
            $techniques[] = 'Sight fishing in clear water';
            $techniques[] = 'Topwater popping and stickbaiting';
        } elseif ($windSpeed < 20) {
            $techniques[] = 'Use medium weight sinkers';
            $techniques[] = 'Mid-water jigging techniques';
            $techniques[] = 'Drift fishing with live bait';
            $techniques[] = 'Bottom bouncing techniques';
        } else {
            $techniques[] = 'Use heavy sinkers (60-100g)';
            $techniques[] = 'Short casting techniques';
            $techniques[] = 'Bottom fishing in deeper waters';
            $techniques[] = 'Heavy duty trolling methods';
        }

        return array_unique($techniques);
    }

    protected function generateSafetyAlerts($weatherMain, $windSpeed)
    {
        $alerts = [];

        if (!$weatherMain || !$windSpeed) {
            return ['Weather data unavailable - Exercise caution'];
        }

        // Safety alerts adapted for Philippine conditions
        if ($windSpeed > 30) {
            $alerts[] = 'PAGASA Warning: Strong winds - Fishing not recommended';
        } elseif ($windSpeed > 20) {
            $alerts[] = 'Moderate wind conditions - Exercise caution';
        }

        if ($weatherMain === 'Thunderstorm') {
            $alerts[] = 'Thunderstorm warning - Seek immediate shelter';
            $alerts[] = 'Risk of lightning strikes - Avoid open waters';
        }

        if ($weatherMain === 'Rain' && $windSpeed > 15) {
            $alerts[] = 'Heavy rain and wind - Be careful of slippery conditions';
            $alerts[] = 'Check PAGASA updates for potential weather disturbances';
        }

        // Add monsoon-specific alerts
        $month = now()->month;
        if (in_array($month, [6, 7, 8, 9])) { // Southwest Monsoon (Habagat)
            $alerts[] = 'Habagat season - Monitor PAGASA updates regularly';
        } elseif (in_array($month, [10, 11, 12, 1, 2])) { // Northeast Monsoon (Amihan)
            $alerts[] = 'Amihan season - Check wind conditions before sailing';
        }

        return $alerts;
    }

    protected function getLocationFromCoordinates($lat, $lon)
    {
        try {
            $response = Http::get("{$this->geocodingUrl}/reverse", [
                'latitude' => $lat,
                'longitude' => $lon,
                'language' => 'en'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'])) {
                    $result = $data['results'][0];
                    $location = $result['name'];
                    
                    // Add municipality/city if available and different from name
                    if (!empty($result['municipality']) && $result['municipality'] !== $result['name']) {
                        $location = $result['municipality'];
                    }
                    
                    // Add admin1 (province/region) if available
                    if (!empty($result['admin1'])) {
                        $location .= ", " . $result['admin1'];
                    }
                    
                    // Always add Philippines
                    $location .= ", Philippines";
                    
                    return $location;
                }
            }

            \Log::warning('Reverse geocoding failed', [
                'lat' => $lat,
                'lon' => $lon,
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            return "Location at {$lat}°N, {$lon}°E";

        } catch (\Exception $e) {
            \Log::error('Reverse geocoding exception', [
                'message' => $e->getMessage(),
                'lat' => $lat,
                'lon' => $lon
            ]);
            return "Location at {$lat}°N, {$lon}°E";
        }
    }

    public function getCurrentWeatherByCoordinates($lat, $lon)
    {
        try {
            \Log::info('Fetching weather by coordinates', [
                'lat' => $lat,
                'lon' => $lon
            ]);

            $response = Http::get("{$this->baseUrl}/forecast", [
                'latitude' => $lat,
                'longitude' => $lon,
                'current' => true,
                'hourly' => 'temperature_2m,relative_humidity_2m,weathercode,wind_speed_10m,wind_direction_10m,cloud_cover',
                'timezone' => 'Asia/Manila'
            ]);

            \Log::info('Weather API response', [
                'status' => $response->status(),
                'url' => $response->effectiveUri(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Get the current hour index
                $currentTime = now()->setTimezone('Asia/Manila');
                $startTime = \Carbon\Carbon::parse($data['hourly']['time'][0]);
                $hourIndex = $currentTime->diffInHours($startTime);
                
                // Get location name from coordinates
                $location = $this->getLocationFromCoordinates($lat, $lon) ?? 'Unknown Location';
                
                // Transform Open-Meteo format to our standard format
                return [
                    'main' => [
                        'temp' => $data['hourly']['temperature_2m'][$hourIndex] ?? null,
                        'humidity' => $data['hourly']['relative_humidity_2m'][$hourIndex] ?? null
                    ],
                    'wind' => [
                        'speed' => $data['hourly']['wind_speed_10m'][$hourIndex] ?? null,
                        'deg' => $data['hourly']['wind_direction_10m'][$hourIndex] ?? null
                    ],
                    'clouds' => [
                        'all' => $data['hourly']['cloud_cover'][$hourIndex] ?? null
                    ],
                    'weather' => [
                        [
                            'main' => $this->getWeatherDescription($data['hourly']['weathercode'][$hourIndex]),
                            'description' => $this->getWeatherDescription($data['hourly']['weathercode'][$hourIndex])
                        ]
                    ],
                    'local_time' => $currentTime->format('h:i A'),
                    'location' => $location,
                    'coordinates' => [
                        'lat' => $lat,
                        'lon' => $lon
                    ]
                ];
            }

            throw new \Exception('Failed to fetch weather data from API: ' . ($response->json()['reason'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            \Log::error('Weather API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lat' => $lat,
                'lon' => $lon
            ]);
            throw $e;
        }
    }

    public function getForecastByCoordinates($lat, $lon)
    {
        try {
            \Log::info('Fetching forecast by coordinates', [
                'lat' => $lat,
                'lon' => $lon
            ]);

            $response = Http::get("{$this->baseUrl}/forecast", [
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => 'temperature_2m_max,temperature_2m_min,weathercode,wind_speed_10m_max',
                'timezone' => 'Asia/Manila'
            ]);

            \Log::info('Forecast API response', [
                'status' => $response->status(),
                'url' => $response->effectiveUri(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $forecast = [];
                
                // Get location name from coordinates
                $location = $this->getLocationFromCoordinates($lat, $lon) ?? 'Unknown Location';
                
                // Transform the daily forecast data
                for ($i = 0; $i < count($data['daily']['time']); $i++) {
                    $forecast[] = [
                        'dt' => strtotime($data['daily']['time'][$i]),
                        'main' => [
                            'temp' => ($data['daily']['temperature_2m_max'][$i] + $data['daily']['temperature_2m_min'][$i]) / 2,
                            'temp_min' => $data['daily']['temperature_2m_min'][$i],
                            'temp_max' => $data['daily']['temperature_2m_max'][$i]
                        ],
                        'wind' => [
                            'speed' => $data['daily']['wind_speed_10m_max'][$i]
                        ],
                        'weather' => [
                            [
                                'main' => $this->getWeatherDescription($data['daily']['weathercode'][$i]),
                                'description' => $this->getWeatherDescription($data['daily']['weathercode'][$i])
                            ]
                        ]
                    ];
                }

                return [
                    'list' => $forecast,
                    'location' => $location,
                    'coordinates' => [
                        'lat' => $lat,
                        'lon' => $lon
                    ]
                ];
            }

            throw new \Exception('Failed to fetch forecast data from API: ' . ($response->json()['reason'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            \Log::error('Forecast API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lat' => $lat,
                'lon' => $lon
            ]);
            throw $e;
        }
    }

    protected function getWeatherDescription($code)
    {
        return match($code) {
            0 => 'Clear',
            1, 2, 3 => 'Clouds',
            45, 48 => 'Foggy',
            51, 53, 55, 56, 57 => 'Drizzle',
            61, 63, 65, 66, 67 => 'Rain',
            71, 73, 75, 77 => 'Snow',
            80, 81, 82 => 'Rain Showers',
            85, 86 => 'Snow Showers',
            95 => 'Thunderstorm',
            96, 99 => 'Thunderstorm with Hail',
            default => 'Unknown'
        };
    }

    public function getMarineWeatherByCoordinates($lat, $lon)
    {
        $cacheKey = "marine_weather_lat{$lat}_lon{$lon}";
        
        return Cache::remember($cacheKey, 3600, function () use ($lat, $lon) {
            try {
                $response = Http::get("{$this->baseUrl}/marine", [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'hourly' => 'wave_height,wave_direction,wave_period',
                    'timezone' => 'Asia/Manila'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $marine = [];

                    // Get the next 24 hours of marine conditions
                    for ($i = 0; $i < 24; $i++) {
                        $marine[] = [
                            'timestamp' => strtotime($data['hourly']['time'][$i]),
                            'wave_height' => $data['hourly']['wave_height'][$i],
                            'wave_direction' => $data['hourly']['wave_direction'][$i],
                            'wave_period' => $data['hourly']['wave_period'][$i]
                        ];
                    }

                    return $marine;
                }

                \Log::error('Marine API error', [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);

                return null;
            } catch (\Exception $e) {
                \Log::error('Marine weather fetch error: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function getAlertsByCoordinates($lat, $lon)
    {
        // Open-Meteo free tier doesn't include weather alerts
        // Return an empty array to maintain compatibility
        return [];
    }

    protected function getCoordinates($location)
    {
        try {
            // Remove country suffix for better search results
            $searchLocation = str_replace([',Philippines', ',PH'], '', $location);
            
            $response = Http::get("{$this->geocodingUrl}/search", [
                'name' => $searchLocation,
                'count' => 1,
                'language' => 'en',
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'])) {
                    return [
                        'latitude' => $data['results'][0]['latitude'],
                        'longitude' => $data['results'][0]['longitude']
                    ];
                }
            }

            \Log::error('Geocoding error', [
                'location' => $location,
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('Geocoding exception', [
                'message' => $e->getMessage(),
                'location' => $location
            ]);
            return null;
        }
    }

    public function getMarineWeather($location = null)
    {
        try {
            $location = $this->validateAndFormatLocation($location);
            $cacheKey = "marine_weather_{$location}";
            
            return Cache::remember($cacheKey, 3600, function () use ($location) {
                // Get coordinates from location
                $coordinates = $this->getCoordinates($location);
                if (!$coordinates) {
                    return null;
                }

                $response = Http::get("{$this->baseUrl}/marine", [
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                    'hourly' => 'wave_height,wave_direction,wave_period',
                    'timezone' => 'Asia/Manila'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $marine = [];

                    // Get the next 24 hours of marine conditions
                    for ($i = 0; $i < 24; $i++) {
                        $marine[] = [
                            'timestamp' => strtotime($data['hourly']['time'][$i]),
                            'wave_height' => $data['hourly']['wave_height'][$i],
                            'wave_direction' => $data['hourly']['wave_direction'][$i],
                            'wave_period' => $data['hourly']['wave_period'][$i]
                        ];
                    }

                    return $marine;
                }

                return null;
            });
        } catch (\Exception $e) {
            \Log::error('Marine weather fetch error', [
                'message' => $e->getMessage(),
                'location' => $location
            ]);
            return null;
        }
    }

    public function getAlerts($location = null)
    {
        // Open-Meteo free tier doesn't include weather alerts
        return [];
    }
} 