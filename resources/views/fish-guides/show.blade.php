<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6 px-4 sm:px-0">
            <div class="flex-1 min-w-0">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol role="list" class="flex flex-wrap items-center space-x-4">
                        <li>
                            <div>
                                <a href="{{ route('fish-guides.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span class="sr-only">Fish Guides</span>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-500 truncate">{{ $guide->title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <div class="mt-2 flex flex-col sm:flex-row sm:items-center">
                    <div class="flex-shrink-0 mb-4 sm:mb-0 sm:mr-4">
                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <span class="text-xl font-medium leading-none text-blue-700">{{ substr($guide->user->name, 0, 1) }}</span>
                        </span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">{{ $guide->title }}</h2>
                        <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $guide->user->name }}
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Updated {{ $guide->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex lg:mt-0 lg:ml-4">
                @if(Auth::user()->hasRole('expert') && $guide->user_id === Auth::id())
                <span class="block w-full sm:w-auto sm:ml-3">
                    <a href="{{ route('fish-guides.edit', $guide) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                </span>
                @endif
                @if(Auth::user()->hasRole('admin'))
                <span class="block w-full sm:w-auto sm:ml-3">
                    <form action="{{ route('fish-guides.disable', $guide) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to disable this guide?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Disable
                        </button>
                    </form>
                </span>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Description -->
                <div class="prose max-w-none">
                    <h3 class="text-lg font-medium text-gray-900">Description</h3>
                    <p class="mt-2 text-gray-600">{{ $guide->description }}</p>
                </div>

                <!-- Fish Species -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">Fish Species</h3>
                    <p class="mt-2 text-gray-600">{{ $guide->fish_species }}</p>
                </div>

                <!-- Care Instructions -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">Care Instructions</h3>
                    <p class="mt-2 text-gray-600">{{ $guide->care_instructions }}</p>
                </div>

                <!-- Feeding Guide -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">Feeding Guide</h3>
                    <p class="mt-2 text-gray-600">{{ $guide->feeding_guide }}</p>
                </div>

                <!-- Water Parameters -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">Water Parameters</h3>
                    <dl class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="sm:col-span-1 bg-gray-50 px-4 py-3 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Temperature</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $guide->water_parameters['temperature'] }}</dd>
                        </div>
                        <div class="sm:col-span-1 bg-gray-50 px-4 py-3 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">pH Level</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $guide->water_parameters['ph'] }}</dd>
                        </div>
                        <div class="sm:col-span-1 bg-gray-50 px-4 py-3 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Water Hardness</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $guide->water_parameters['hardness'] }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Common Diseases -->
                @if($guide->common_diseases)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">Common Diseases</h3>
                    <p class="mt-2 text-gray-600">{{ $guide->common_diseases }}</p>
                </div>
                @endif

                <!-- Prevention Tips -->
                @if($guide->prevention_tips)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">Prevention Tips</h3>
                    <p class="mt-2 text-gray-600">{{ $guide->prevention_tips }}</p>
                </div>
                @endif

                <!-- Views Count -->
                <div class="mt-8 flex items-center text-sm text-gray-500">
                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ $guide->views }} views
                </div>

                @if($guide->images->count() > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">Images</h3>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($guide->images as $image)
                        <div class="relative group">
                            <div class="aspect-w-10 aspect-h-7 rounded-lg overflow-hidden">
                                <img src="{{ Storage::url($image->path) }}" alt="{{ $image->caption }}" class="object-cover w-full h-full">
                            </div>
                            @if($image->caption)
                            <p class="mt-2 text-sm text-gray-500">{{ $image->caption }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($guide->references)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">References</h3>
                    <div class="mt-4 prose max-w-none">
                        {!! $guide->references !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Comments Section -->
        @if($guide->status === 'published')
        <div class="mt-6">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900">Comments</h3>
                    
                    <!-- New Comment Form -->
                    <div class="mt-6">
                        <form action="{{ route('fish-guides.comments.store', $guide) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700">Share your thoughts</label>
                                <textarea id="comment" name="content" rows="3" class="shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border border-gray-300 rounded-md @error('content') border-red-500 @enderror" placeholder="Add a comment..."></textarea>
                                @error('content')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div class="mt-8 space-y-6">
                        @forelse($guide->comments->sortByDesc('created_at') as $comment)
                        <div class="flex space-x-3">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-500">
                                    <span class="text-sm font-medium leading-none text-white">{{ substr($comment->user->name, 0, 1) }}</span>
                                </span>
                            </div>
                            <div class="flex-grow">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                                    @if($comment->user->hasRole('expert'))
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Expert</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-sm text-gray-700">
                                    <p>{{ $comment->content }}</p>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">No comments yet. Be the first to comment!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>