<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Welcome Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-2">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800">Expert Dashboard</h2>
            <p class="mt-2 text-gray-600">Manage fish guides and review catch analyses</p>
        </div>
    </div>

    <!-- Fish Guides Management -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Your Fish Guides</h3>
                <a href="{{ route('fish-guides.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Guide
                </a>
            </div>
            @php
                $myGuides = auth()->user()->fishGuides()->latest()->take(5)->get();
            @endphp
            @if($myGuides->count() > 0)
                <div class="space-y-4">
                    @foreach($myGuides as $guide)
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-md font-medium text-gray-800">{{ $guide->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ Str::limit($guide->description, 100) }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('fish-guides.edit', $guide) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('fish-guides.index') }}" class="text-blue-500 hover:text-blue-700">View all guides →</a>
                </div>
            @else
                <p class="text-gray-600">You haven't created any guides yet. Start by creating your first guide!</p>
            @endif
        </div>
    </div>

    <!-- Recent Catch Analysis Reviews -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Catch Analyses</h3>
            @php
                $recentCatches = App\Models\CatchAnalysis::latest()->take(5)->get();
            @endphp
            @if($recentCatches->count() > 0)
                <div class="space-y-4">
                    @foreach($recentCatches as $catch)
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-md font-medium text-gray-800">{{ $catch->fish_species }}</h4>
                                    <p class="text-sm text-gray-600">By {{ $catch->user->name }} on {{ $catch->catch_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('catch-analyses.show', $catch) }}" class="text-blue-500 hover:text-blue-700">Review →</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('catch-analyses.index') }}" class="text-blue-500 hover:text-blue-700">View all analyses →</a>
                </div>
            @else
                <p class="text-gray-600">No catch analyses to review.</p>
            @endif
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-2">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total Guides Created</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ auth()->user()->fishGuides()->count() }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Guides Views</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ App\Models\FishGuide::sum('views') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Analyses Reviewed</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ App\Models\CatchAnalysis::where('reviewed', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div> 