<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ auth()->user()->createToken('api-token')->plainTextToken }}">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Catch Analysis Details</h2>
                <div class="flex space-x-4">
                    @can('update', $catchAnalysis)
                        <a href="{{ route('catch-analyses.edit', $catchAnalysis) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Record
                        </a>
                    @endcan
                    @can('delete', $catchAnalysis)
                        <form action="{{ route('catch-analyses.destroy', $catchAnalysis) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this record?')">
                                Delete Record
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @if($catchAnalysis->image_path)
                    <div class="lg:col-span-1 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">Catch Photo</h3>
                        <div class="relative group">
                            <a href="#" onclick="showModal('{{ Storage::url($catchAnalysis->image_path) }}')" class="block">
                                <img 
                                    src="{{ Storage::url($catchAnalysis->image_path) }}" 
                                    alt="Catch Photo" 
                                    class="w-full h-auto rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"
                                >
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black bg-opacity-20 rounded-lg">
                                    <div class="bg-black bg-opacity-50 text-white px-4 py-2 rounded-lg">
                                        Click to enlarge
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="lg:col-span-2">
                @else
                    <div class="lg:col-span-3">
                @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">Basic Information</h3>
                            <dl class="grid grid-cols-1 gap-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fish Species</dt>
                                    <dd class="text-sm text-gray-900">{{ $catchAnalysis->fish_species }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Catch Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $catchAnalysis->catch_date->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="text-sm text-gray-900">{{ $catchAnalysis->location }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">Catch Metrics</h3>
                            <dl class="grid grid-cols-1 gap-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                                    <dd class="text-sm text-gray-900">{{ $catchAnalysis->quantity }} fish</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Weight</dt>
                                    <dd class="text-sm text-gray-900">{{ $catchAnalysis->total_weight }} kg</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Average Size</dt>
                                    <dd class="text-sm text-gray-900">{{ $catchAnalysis->average_size }} kg</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">Environmental Conditions</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Weather Conditions</dt>
                                    <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                        @php
                                            $weatherIcon = match(strtolower(trim($catchAnalysis->weather_conditions))) {
                                                'sunny', 'clear' => '<svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>',
                                                'cloudy', 'overcast' => '<svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/></svg>',
                                                'rainy', 'rain' => '<svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M3.5 16a3.5 3.5 0 100-7 3.5 3.5 0 000 7zm9-7a3.5 3.5 0 100-7 3.5 3.5 0 000 7z M13.5 16a3.5 3.5 0 100-7 3.5 3.5 0 000 7z"/></svg>',
                                                'stormy', 'storm' => '<svg class="w-5 h-5 text-gray-700 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z"/></svg>',
                                                default => '<svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/></svg>'
                                            };
                                        @endphp
                                        {!! $weatherIcon !!}
                                        {{ $catchAnalysis->weather_conditions }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fishing Conditions Rating</dt>
                                    <dd class="mt-1">
                                        @php
                                            $weatherCondition = strtolower(trim($catchAnalysis->weather_conditions));
                                            $conditionRating = match($weatherCondition) {
                                                'sunny', 'clear', 'fair' => ['Excellent', 'bg-green-100 text-green-800'],
                                                'cloudy', 'overcast', 'partly cloudy' => ['Good', 'bg-blue-100 text-blue-800'],
                                                'light rain', 'drizzle', 'rainy', 'rain', 'moderate rain' => ['Fair', 'bg-yellow-100 text-yellow-800'],
                                                'heavy rain', 'stormy', 'storm', 'thunderstorm' => ['Poor', 'bg-red-100 text-red-800'],
                                                '' => ['Unknown', 'bg-gray-100 text-gray-800'],
                                                default => ['Moderate', 'bg-blue-100 text-blue-800']
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $conditionRating[1] }}">
                                            {{ $conditionRating[0] }}
                                        </span>
                                    </dd>
                                </div>
                                {{-- <div>
                                    <dt class="text-sm font-medium text-gray-500">Catch Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $catchAnalysis->catch_date->format('h:i A') }}</dd>
                                </div> --}}
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Season</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @php
                                            $month = $catchAnalysis->catch_date->format('n');
                                            $season = match(true) {
                                                in_array($month, [12, 1, 2]) => 'Winter',
                                                in_array($month, [3, 4, 5]) => 'Spring',
                                                in_array($month, [6, 7, 8]) => 'Summer',
                                                in_array($month, [9, 10, 11]) => 'Fall',
                                                default => 'Unknown'
                                            };
                                        @endphp
                                        {{ $season }}
                                    </dd>
                                </div>

                                <!-- Weather Analysis Section -->
                                <div class="col-span-full mt-4 border-t pt-4">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">Weather Analysis & Recommendations</dt>
                                    <dd class="mt-1 space-y-4">
                                        @php
                                            $weatherAnalysis = match(strtolower(trim($catchAnalysis->weather_conditions))) {
                                                'sunny', 'clear' => [
                                                    'title' => 'Optimal Fishing Conditions',
                                                    'description' => 'Clear skies provide excellent visibility for surface fishing. Fish may be more active in shallow waters during early morning and late afternoon.',
                                                    'tips' => [
                                                        'Best for surface lures and fly fishing',
                                                        'Focus on shaded areas where fish might seek cover',
                                                        'Consider using lighter line as fish may be more line-shy in clear conditions'
                                                    ],
                                                    'color' => 'text-green-700'
                                                ],
                                                'cloudy', 'overcast' => [
                                                    'title' => 'Good Fishing Conditions',
                                                    'description' => 'Overcast conditions can trigger increased feeding activity as fish feel more secure under cloud cover.',
                                                    'tips' => [
                                                        'Effective for both surface and subsurface fishing',
                                                        'Fish may be more active throughout the water column',
                                                        'Consider using darker lures for better silhouettes'
                                                    ],
                                                    'color' => 'text-blue-700'
                                                ],
                                                'rainy', 'rain' => [
                                                    'title' => 'Challenging but Potentially Productive',
                                                    'description' => 'Rain can increase oxygen levels and wash food into the water, potentially triggering feeding activity.',
                                                    'tips' => [
                                                        'Focus on areas where runoff enters the water',
                                                        'Use larger, more visible lures',
                                                        'Pay attention to water clarity changes'
                                                    ],
                                                    'color' => 'text-yellow-700'
                                                ],
                                                'stormy', 'storm' => [
                                                    'title' => 'Difficult Fishing Conditions',
                                                    'description' => 'Stormy weather can be dangerous and typically results in reduced fishing success. Consider safety first.',
                                                    'tips' => [
                                                        'Not recommended for fishing - prioritize safety',
                                                        'If conditions improve, fish the edges of weather systems',
                                                        'Monitor weather changes closely'
                                                    ],
                                                    'color' => 'text-red-700'
                                                ],
                                                default => [
                                                    'title' => 'General Fishing Conditions',
                                                    'description' => 'Adapt your fishing strategy based on local conditions and fish behavior.',
                                                    'tips' => [
                                                        'Observe local fish activity',
                                                        'Test different depths and techniques',
                                                        'Monitor water conditions'
                                                    ],
                                                    'color' => 'text-gray-700'
                                                ]
                                            };
                                        @endphp
                                        
                                        <div class="bg-white rounded-lg p-4 shadow-sm">
                                            <h4 class="font-medium {{ $weatherAnalysis['color'] }}">{{ $weatherAnalysis['title'] }}</h4>
                                            <p class="text-sm text-gray-600 mt-2">{{ $weatherAnalysis['description'] }}</p>
                                            
                                            <div class="mt-3">
                                                <h5 class="text-sm font-medium text-gray-700">Recommendations:</h5>
                                                <ul class="mt-2 space-y-1">
                                                    @foreach($weatherAnalysis['tips'] as $tip)
                                                        <li class="text-sm text-gray-600 flex items-center">
                                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            {{ $tip }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">Additional Information</h3>
                            <dl class="grid grid-cols-1 gap-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                    <dd class="text-sm text-gray-900 whitespace-pre-line">{{ $catchAnalysis->notes ?: 'No additional notes.' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expert Reviews Section -->
            <div class="mt-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Expert Reviews</h3>
                
                @forelse($catchAnalysis->expertReviews as $review)
                    <div class="bg-gray-50 p-6 rounded-lg mb-4">
                        <button
                            type="button"
                            onclick="toggleReview('review-{{ $review->id }}')"
                            class="w-full flex items-center justify-between text-left focus:outline-none"
                        >
                            <div class="flex items-center">
                                <span class="text 6lg font-semibold text-gray-800">Review by {{ $review->reviewer->name }}</span>
                                <span class="ml-3 text-sm text-gray-500">Click to view</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Sustainability:</span>
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($review->sustainability_rating === 'Good') bg-green-100 text-green-800
                                    @elseif($review->sustainability_rating === 'Concerning') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $review->sustainability_rating }}
                                </span>
                                <svg class="review-arrow w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        <div id="review-{{ $review->id }}" class="hidden mt-4">
                            <div class="space-y-6">
                                <!-- Analysis Section -->
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h4 class="text-md font-medium text-gray-700 mb-3">Analysis & Observations</h4>
                                    <div class="prose prose-sm max-w-none text-gray-600">
                                        @php
                                            $paragraphs = $review->feedback ? explode("\n\n", $review->feedback) : [];
                                        @endphp
                                        @foreach($paragraphs as $paragraph)
                                            <p class="mb-2">{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Recommendations Section -->
                                @php
                                    $suggestions = $review->suggestions;
                                @endphp
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h4 class="text-md font-medium text-gray-700 mb-3">Recommendations</h4>
                                    <ul class="space-y-2">
                                        @foreach($suggestions as $suggestion)
                                            @if(trim($suggestion->recommendations))
                                                <li class="flex items-start">
                                                    <span class="flex-shrink-0 w-4 h-4 mt-1 mr-2">
                                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                    </span>
                                                    <span class="text-gray-600">{{ trim($suggestion->recommendations) }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Review Meta Information -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>Reviewed by {{ $review->reviewer->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ $review->created_at->format('M d, Y H:i') }}</span>
                                        </div>
                                    </div>

                                    @if(auth()->id() === $review->reviewer_id || auth()->user()->hasRole('admin'))
                                        <div class="mt-4 flex justify-end">
                                            <form action="{{ route('catch-analyses.unreview', $catchAnalysis) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-red-300 text-sm text-red-600 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Remove Review
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50 p-6 rounded-lg text-gray-500 text-center">
                        No expert reviews yet.
                    </div>
                @endforelse
            </div>

            @push('scripts')
            <script>
                function toggleReview(reviewId) {
                    const content = document.getElementById(reviewId);
                    const arrow = content.parentElement.querySelector('.review-arrow');
                    if (content.classList.contains('hidden')) {
                        content.classList.remove('hidden');
                        arrow.classList.add('rotate-180');
                    } else {
                        content.classList.add('hidden');
                        arrow.classList.remove('rotate-180');
                    }
                }
            </script>
            @endpush

            @can('review', $catchAnalysis)
                <!-- Expert Review Form -->
                <div class="mt-8 bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Expert Review</h3>
                        <button type="button" 
                            id="suggestButton"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Get Suggestions
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div id="loading" class="hidden mb-4">
                        <div class="flex items-center justify-center p-4 bg-blue-50 rounded-lg">
                            <svg class="animate-spin h-5 w-5 text-blue-500 mr-3" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                            <span class="text-blue-700">Generating suggestions...</span>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div id="error" class="hidden mb-4">
                        <div class="flex items-center justify-center p-4 bg-red-50 rounded-lg">
                            <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span id="errorMessage" class="text-red-700"></span>
                        </div>
                    </div>

                    <!-- Add this after the loading and error states -->
                    <div id="aiStats" class="hidden mb-4">
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-blue-700">AI Suggestion Stats:</span>
                            </div>
                            <div class="flex space-x-4">
                                <span class="text-sm text-blue-600">
                                    Confidence: <span id="confidenceScore" class="font-medium">-</span>
                                </span>
                                <span class="text-sm text-blue-600">
                                    Expert Cases: <span id="expertCases" class="font-medium">-</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div id="noSuggestionsMessage" class="hidden mb-4 p-4 bg-yellow-50 text-yellow-800 rounded-lg">
                        No suggestions available for this analysis.
                    </div>

                    <form action="{{ route('catch-analyses.review', $catchAnalysis) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label for="feedback" class="block text-sm font-medium text-gray-700">Expert Feedback</label>
                                <div class="mt-1 relative">
                                    <textarea 
                                        id="feedback" 
                                        name="feedback" 
                                        rows="5" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                        placeholder="Provide your expert analysis..."></textarea>
                                    <div class="absolute top-0 right-0 mt-1 mr-1">
                                        <div id="suggestionBadge1" class="hidden px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                            Suggested
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="recommendations" class="block text-sm font-medium text-gray-700">Recommendations</label>
                                <div class="mt-1 relative">
                                    <textarea 
                                        id="recommendations" 
                                        name="recommendations" 
                                        rows="4" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                        placeholder="Provide recommendations for improvement..."></textarea>
                                    <div class="absolute top-0 right-0 mt-1 mr-1">
                                        <div id="suggestionBadge2" class="hidden px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                            Suggested
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="sustainability_rating" class="block text-sm font-medium text-gray-700">Sustainability Rating</label>
                                <select id="sustainability_rating" name="sustainability_rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">Select a rating...</option>
                                    <option value="Good" class="text-green-700">Good - Sustainable practices</option>
                                    <option value="Concerning" class="text-yellow-700">Concerning - Needs improvement</option>
                                    <option value="Critical" class="text-red-700">Critical - Immediate action required</option>
                                </select>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" 
                                    id="resetForm"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Reset
                                </button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Submit Review
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const catchAnalysisId = {{ $catchAnalysis->id }};
                        const suggestButton = document.getElementById('suggestButton');
                        const loading = document.getElementById('loading');
                        const error = document.getElementById('error');
                        const errorMessage = document.getElementById('errorMessage');
                        const expertFeedback = document.getElementById('feedback');
                        const recommendations = document.getElementById('recommendations');
                        const sustainabilityRating = document.getElementById('sustainability_rating');
                        const resetForm = document.getElementById('resetForm');
                        const suggestionBadge1 = document.getElementById('suggestionBadge1');
                        const suggestionBadge2 = document.getElementById('suggestionBadge2');

                        let originalValues = {
                            feedback: '',
                            recommendations: '',
                            rating: ''
                        };

                        // Store original values when they're manually entered
                        expertFeedback.addEventListener('input', () => {
                            originalValues.feedback = expertFeedback.value;
                            suggestionBadge1.classList.add('hidden');
                        });

                        recommendations.addEventListener('input', () => {
                            originalValues.recommendations = recommendations.value;
                            suggestionBadge2.classList.add('hidden');
                        });

                        sustainabilityRating.addEventListener('change', () => {
                            originalValues.rating = sustainabilityRating.value;
                        });

                        // Reset form to original values
                        resetForm.addEventListener('click', () => {
                            expertFeedback.value = originalValues.feedback;
                            recommendations.value = originalValues.recommendations;
                            sustainabilityRating.value = originalValues.rating;
                            suggestionBadge1.classList.add('hidden');
                            suggestionBadge2.classList.add('hidden');
                            error.classList.add('hidden');
                        });

                        // Get Suggestions Button Click Handler
                        suggestButton.addEventListener('click', async () => {
                            try {
                                // Reset states
                                error.classList.add('hidden');
                                document.getElementById('aiStats').classList.add('hidden');
                                
                                // Show loading state
                                loading.classList.remove('hidden');
                                suggestButton.disabled = true;

                                const response = await fetch(`/catch-analyses/${catchAnalysisId}/suggestions`, {
                                    method: 'GET',
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                });

                                if (!response.ok) {
                                    throw new Error('Failed to get suggestions');
                                }

                                const data = await response.json();

                                // Update form fields with suggestions
                                let hasSuggestions = false;
                                if (data.feedback) {
                                    expertFeedback.value = data.feedback;
                                    suggestionBadge1.classList.remove('hidden');
                                    hasSuggestions = true;
                                } else {
                                    expertFeedback.value = '';
                                    suggestionBadge1.classList.add('hidden');
                                }

                                if (data.recommendations) {
                                    recommendations.value = data.recommendations;
                                    suggestionBadge2.classList.remove('hidden');
                                    hasSuggestions = true;
                                } else {
                                    recommendations.value = '';
                                    suggestionBadge2.classList.add('hidden');
                                }

                                if (data.sustainability_rating) {
                                    sustainabilityRating.value = data.sustainability_rating;
                                }

                                // Show/hide no suggestions message
                                const noSuggestionsMessage = document.getElementById('noSuggestionsMessage');
                                if (!hasSuggestions) {
                                    noSuggestionsMessage.classList.remove('hidden');
                                } else {
                                    noSuggestionsMessage.classList.add('hidden');
                                }

                                // Show AI stats if available
                                if (data.confidence_score !== null || data.based_on_expert_cases !== null) {
                                    document.getElementById('confidenceScore').textContent = 
                                        data.confidence_score !== null ? `${(data.confidence_score * 100).toFixed(1)}%` : 'N/A';
                                    document.getElementById('expertCases').textContent = 
                                        data.based_on_expert_cases !== null ? data.based_on_expert_cases : 'N/A';
                                    document.getElementById('aiStats').classList.remove('hidden');
                                }

                                // Store suggestions as original values
                                originalValues = {
                                    feedback: expertFeedback.value,
                                    recommendations: recommendations.value,
                                    rating: sustainabilityRating.value
                                };

                            } catch (error) {
                                console.error('Error getting suggestions:', error);
                                errorMessage.textContent = 'Failed to get suggestions. Please try again.';
                                error.classList.remove('hidden');
                            } finally {
                                loading.classList.add('hidden');
                                suggestButton.disabled = false;
                            }
                        });
                    });
                </script>
                @endpush
            @endif

            <div class="mt-6">
                <a href="{{ route('catch-analyses.index') }}" class="text-blue-500 hover:text-blue-700">
                    ← Back to Catch Analysis Records
                </a>
            </div>
        </div>
    </div>

    <!-- Simple Modal -->
    <div id="modal" class="fixed inset-0 hidden z-50">
        <!-- Modal background -->
        <div class="fixed inset-0 bg-black opacity-75"></div>
        
        <!-- Modal content -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <!-- Close button -->
            <button onclick="hideModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Image container -->
            <div class="relative max-w-[90vw] max-h-[90vh]">
                <img id="modal-image" src="" alt="Enlarged catch photo" 
                    class="max-w-full max-h-[85vh] object-contain rounded-lg"
                    style="margin: auto;">
            </div>
        </div>
    </div>

    <script>
        function showModal(imageSrc) {
            event.preventDefault();
            document.getElementById('modal-image').src = imageSrc;
            document.getElementById('modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideModal() {
            document.getElementById('modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideModal();
            }
        });

        // Close on background click
        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal();
            }
        });
    </script>
</x-app-layout> 