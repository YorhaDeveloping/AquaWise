<x-app-layout>
    <!-- Add MapTiler and OpenStreetMap CSS in the layout head -->
    @push('styles')
    <style>
        .location-info {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Location Search -->
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Weather Forecast</h2>
                        <!-- Search Form -->
                        <div class="max-w-md">
                            <form action="{{ route('weather.index') }}" method="GET" class="space-y-4">
                                <div class="relative">
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <input type="text" 
                                            name="location" 
                                            id="location" 
                                            value="{{ $location }}"
                                            class="block w-full pr-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="Enter city name (e.g., Manila)"
                                            required
                                            pattern="[A-Za-z\s]+"
                                            title="Please enter a valid city name using letters and spaces only" disabled>
                                        <div class="absolute inset-y-0 right-0 flex items-center">
                                            <button type="submit"
                                                class="inline-flex items-center px-4 h-full border border-transparent text-sm font-medium rounded-r-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">City name will be automatically suffixed with ", Philippines"</p>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-6"></div>

                    <!-- Weather Component Container -->
                    <div class="weather-component-container">
                        <x-weather-forecast 
                            :weather="$weather" 
                            :forecast="$forecast" 
                            :marine="$marine" 
                            :alerts="$alerts" 
                        />
                    </div>

                    <!-- Detailed Analysis -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Fishing Recommendations</h3>
                        
                        <!-- Activity Level Card -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xl font-semibold text-gray-900">Current Fishing Conditions</h4>
                                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $fishingConditions['activity_level'] === 'High' ? 'bg-green-100 text-green-800' : ($fishingConditions['activity_level'] === 'Moderate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $fishingConditions['activity_level'] }} Activity
                                </span>
                            </div>
                            
                            <div class="space-y-4">
                                <!-- Best Time -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h5 class="text-sm font-medium text-gray-900">Best Fishing Time</h5>
                                        <p class="mt-1 text-sm text-gray-500">{{ $fishingConditions['best_time'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Recommended Spots -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <h4 class="ml-2 text-lg font-medium text-gray-900">Recommended Spots</h4>
                                </div>
                                @if(!empty($fishingConditions['recommended_spots']))
                                    <ul class="space-y-3">
                                        @foreach($fishingConditions['recommended_spots'] as $spot)
                                            <li class="flex items-start">
                                                <svg class="h-5 w-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span class="ml-2 text-gray-700">{{ $spot }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500 text-sm">No specific spots recommended for current conditions</p>
                                @endif
                            </div>

                            <!-- Recommended Techniques -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <h4 class="ml-2 text-lg font-medium text-gray-900">Recommended Techniques</h4>
                                </div>
                                @if(!empty($fishingConditions['recommended_techniques']))
                                    <ul class="space-y-3">
                                        @foreach($fishingConditions['recommended_techniques'] as $technique)
                                            <li class="flex items-start">
                                                <svg class="h-5 w-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span class="ml-2 text-gray-700">{{ $technique }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500 text-sm">No specific techniques recommended for current conditions</p>
                                @endif
                            </div>
                        </div>

                        <!-- Safety Alerts -->
                        @if(!empty($fishingConditions['safety_alerts']))
                        <div class="mt-6">
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-red-800">Safety Alerts</h3>
                                        <div class="mt-2">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach($fishingConditions['safety_alerts'] as $alert)
                                                    <li class="text-red-700">{{ $alert }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 