<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Welcome Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-2">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800">Admin Dashboard</h2>
            <p class="mt-2 text-gray-600">System overview and management</p>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-2">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Overview</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ App\Models\User::count() }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total Experts</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ App\Models\User::role('expert')->count() }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total Guides</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ App\Models\FishGuide::count() }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total Analyses</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ App\Models\CatchAnalysis::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Users</h3>
                <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add User
                </a>
            </div>
            @php
                $recentUsers = App\Models\User::latest()->take(5)->get();
            @endphp
            <div class="space-y-4">
                @foreach($recentUsers as $user)
                    <div class="border-b border-gray-200 pb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-md font-medium text-gray-800">{{ $user->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                <p class="text-xs text-gray-500">Joined: {{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('users.index') }}" class="text-blue-500 hover:text-blue-700">View all users →</a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
            <div class="space-y-4">
                @php
                    $recentGuides = App\Models\FishGuide::latest()->take(3)->get();
                    $recentAnalyses = App\Models\CatchAnalysis::where('reviewed', false)->latest()->take(3)->get();
                @endphp
                
                @foreach($recentGuides as $guide)
                    <div class="border-b border-gray-200 pb-4">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                            <div>
                                <p class="text-sm text-gray-800">New guide: {{ $guide->title }}</p>
                                <p class="text-xs text-gray-600">by {{ $guide->user->name }} - {{ $guide->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                @foreach($recentAnalyses as $analysis)
                    <div class="border-b border-gray-200 pb-4">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <div>
                                <p class="text-sm text-gray-800">Pending review: {{ $analysis->fish_species }}</p>
                                <p class="text-xs text-gray-600">by {{ $analysis->user->name }} - {{ $analysis->created_at->diffForHumans() }}</p>
                                <a href="{{ route('catch-analyses.show', $analysis) }}" class="text-blue-500 hover:text-blue-700 text-xs">Review now →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div> 