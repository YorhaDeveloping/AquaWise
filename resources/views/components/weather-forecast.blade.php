@props(['weather', 'forecast', 'marine', 'alerts'])

<div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <div class="p-4">
        <h3 class="text-lg font-semibold text-gray-900">Weather Forecast & Fishing Conditions</h3>
        
        @if($weather)
            <p class="text-sm text-gray-600">Location: {{ $weather['location']['name'] }}, {{ $weather['location']['region'] }}</p>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Current Conditions -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-medium text-blue-900">Current Conditions</h4>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Temperature</span>
                            <span class="text-sm font-medium text-gray-900">{{ $weather['temperature'] }}{{ $weather['units']['temperature'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Feels Like</span>
                            <span class="text-sm font-medium text-gray-900">{{ $weather['feels_like'] }}{{ $weather['units']['temperature'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Wind Speed</span>
                            <span class="text-sm font-medium text-gray-900">{{ $weather['wind_speed'] }} {{ $weather['units']['wind_speed'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Humidity</span>
                            <span class="text-sm font-medium text-gray-900">{{ $weather['humidity'] }}{{ $weather['units']['humidity'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Precipitation</span>
                            <span class="text-sm font-medium text-gray-900">{{ $weather['precipitation'] }}{{ $weather['units']['precipitation'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Marine Conditions -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-medium text-green-900">Marine Conditions</h4>
                    <div class="mt-2 space-y-2">
                        @if(!empty($marine))
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Wave Height</span>
                                <span class="text-sm font-medium text-gray-900">{{ $marine[0]['wave_height'] }}m</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Wave Period</span>
                                <span class="text-sm font-medium text-gray-900">{{ $marine[0]['wave_period'] }}s</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Swell Height</span>
                                <span class="text-sm font-medium text-gray-900">{{ $marine[0]['swell_wave_height'] }}m</span>
                            </div>
                        @else
                            <p class="text-sm text-gray-600">Marine data not available for this location</p>
                        @endif
                    </div>
                </div>

                <!-- Today's Forecast -->
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h4 class="font-medium text-yellow-900">Today's Forecast</h4>
                    @if(!empty($forecast))
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">High</span>
                                <span class="text-sm font-medium text-gray-900">{{ $forecast[0]['max_temp'] }}°C</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Low</span>
                                <span class="text-sm font-medium text-gray-900">{{ $forecast[0]['min_temp'] }}°C</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Rain Chance</span>
                                <span class="text-sm font-medium text-gray-900">{{ $forecast[0]['precipitation_probability'] }}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">UV Index</span>
                                <span class="text-sm font-medium text-gray-900">{{ $forecast[0]['uv_index'] }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-600">Forecast data not available</p>
                    @endif
                </div>
            </div>

            <!-- 7-Day Forecast -->
            @if(!empty($forecast))
                <div class="mt-6">
                    <h4 class="font-medium text-gray-900 mb-4">7-Day Forecast</h4>
                    <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                        @foreach($forecast as $day)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h5 class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($day['date'])->format('D') }}</h5>
                            <div class="mt-2 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">High/Low</span>
                                    <span class="text-xs font-medium text-gray-900">{{ $day['max_temp'] }}/{{ $day['min_temp'] }}°C</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Rain</span>
                                    <span class="text-xs font-medium text-gray-900">{{ $day['precipitation_probability'] }}%</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Wind</span>
                                    <span class="text-xs font-medium text-gray-900">{{ round($day['max_wind_speed']) }} km/h</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Weather Alerts -->
            @if(!empty($alerts))
                <div class="mt-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Weather Alerts</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    @foreach($alerts as $alert)
                                        <div class="mb-2">
                                            <strong>{{ $alert['title'] }}</strong>
                                            <p>{{ $alert['message'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Weather Data Unavailable</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Unable to fetch weather data for the specified location. Please check the location name and try again.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div> 