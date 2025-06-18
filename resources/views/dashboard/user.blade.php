<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Welcome Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-2">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="mt-2 text-gray-600">Track your catch records and access fish raising guides.</p>
        </div>
    </div>

    <!-- Recent Catch Analysis -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Catch Records</h3>
                <a href="{{ route('catch-analyses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Record
                </a>
            </div>
            @php
                $recentCatches = auth()->user()->catchAnalyses()->latest()->take(5)->get();
            @endphp
            @if($recentCatches->count() > 0)
                <div class="space-y-4">
                    @foreach($recentCatches as $catch)
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-md font-medium text-gray-800">{{ $catch->fish_species }}</h4>
                                    <p class="text-sm text-gray-600">{{ $catch->catch_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-800">{{ $catch->quantity }} fish</p>
                                    <p class="text-sm text-gray-600">{{ $catch->total_weight }} kg</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('catch-analyses.index') }}" class="text-blue-500 hover:text-blue-700">View all records →</a>
                </div>
            @else
                <p class="text-gray-600">No catch records yet. Start by adding your first record!</p>
            @endif
        </div>
    </div>

    <!-- Fish Guides -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Latest Fish Guides</h3>
            @php
                $recentGuides = App\Models\FishGuide::latest()->take(5)->get();
            @endphp
            @if($recentGuides->count() > 0)
                <div class="space-y-4">
                    @foreach($recentGuides as $guide)
                        <div class="border-b border-gray-200 pb-4">
                            <h4 class="text-md font-medium text-gray-800">{{ $guide->title }}</h4>
                            <p class="text-sm text-gray-600">{{ Str::limit($guide->description, 100) }}</p>
                            <div class="mt-2">
                                <a href="{{ route('fish-guides.show', $guide) }}" class="text-blue-500 hover:text-blue-700">Read more →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('fish-guides.index') }}" class="text-blue-500 hover:text-blue-700">View all guides →</a>
                </div>
            @else
                <p class="text-gray-600">No guides available yet.</p>
            @endif
        </div>
    </div>
</div> 