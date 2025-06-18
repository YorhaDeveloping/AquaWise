<nav x-data="{ open: false }" class="bg-blue-700 w-64 min-h-screen flex-shrink-0 hidden sm:block">
    <!-- Sidebar Navigation -->
    <div class="h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between px-4 py-3">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('logo/aquawise.png') }}" alt="AquaWise Logo" class="h-10 w-10 mr-2">
                <span class="text-2xl font-bold text-white">AquaWise</span>
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="mt-5 px-2">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('dashboard') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ __('Dashboard') }}
            </x-nav-link>

            <x-nav-link :href="route('catch-analyses.index')" :active="request()->routeIs('catch-analyses.*')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('catch-analyses.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                {{ __('Catch Analysis') }}
            </x-nav-link>

            <x-nav-link :href="route('fish-guides.index')" :active="request()->routeIs('fish-guides.*')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('fish-guides.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                {{ __('Fish Guides') }}
            </x-nav-link>

            <x-nav-link :href="route('weather.index')" :active="request()->routeIs('weather.*')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('weather.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                </svg>
                {{ __('Weather Forecast') }}
            </x-nav-link>

            <x-nav-link :href="route('ai.consultation.index')" :active="request()->routeIs('ai.consultation.*')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('ai.consultation.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104c-.739-.07-1.461.097-2.032.511a3.034 3.034 0 0 0-1.214 1.978c-.059.482-.036.965.068 1.432.208.928.148 1.879-.169 2.757a6.993 6.993 0 0 1-1.655 2.412 6.492 6.492 0 0 0-1.327 2.31 6.51 6.51 0 0 0-.297 2.724c.108.948.444 1.849.981 2.635.537.786 1.253 1.437 2.093 1.906M14.25 3.104c.739-.07 1.461.097 2.032.511a3.034 3.034 0 0 1 1.214 1.978c.059.482.036.965-.068 1.432-.208.928-.148 1.879.169 2.757a6.993 6.993 0 0 0 1.655 2.412 6.492 6.492 0 0 1 1.327 2.31 6.51 6.51 0 0 1 .297 2.724 6.894 6.894 0 0 1-.981 2.635c-.537.786-1.253 1.437-2.093 1.906m-5.552-4.297c.11-.314.234-.624.372-.928.551-1.214 1.339-2.293 2.328-3.184a7.805 7.805 0 0 0 1.71-2.428 7.83 7.83 0 0 0 .586-2.714"/>
                </svg>
                {{ __('Consult') }}
            </x-nav-link>
        </div>
    </div>
</nav>

<!-- Mobile Navigation -->
<div class="sm:hidden">
    <div x-show="open" class="fixed inset-0 z-40 bg-black bg-opacity-50" @click="open = false"></div>
    
    <div x-show="open" class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-700 overflow-y-auto">
        <!-- Mobile Logo -->
        <div class="flex items-center justify-between px-4 py-3">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <span class="text-2xl font-bold text-white">AquaWise</span>
            </a>
            <button @click="open = false" class="text-white hover:text-gray-200">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Links -->
        <div class="mt-5 px-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('dashboard') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('catch-analyses.index')" :active="request()->routeIs('catch-analyses.*')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('catch-analyses.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                {{ __('Catch Analysis') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('fish-guides.index')" :active="request()->routeIs('fish-guides.*')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('fish-guides.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                {{ __('Fish Guides') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('weather.index')" :active="request()->routeIs('weather.*')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('weather.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                </svg>
                {{ __('Weather Forecast') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('ai.consultation.index')" :active="request()->routeIs('ai.consultation.*')" class="flex items-center text-white py-2 px-4 rounded-lg mb-2 {{ request()->routeIs('ai.consultation.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104c-.739-.07-1.461.097-2.032.511a3.034 3.034 0 0 0-1.214 1.978c-.059.482-.036.965.068 1.432.208.928.148 1.879-.169 2.757a6.993 6.993 0 0 1-1.655 2.412 6.492 6.492 0 0 0-1.327 2.31 6.51 6.51 0 0 0-.297 2.724c.108.948.444 1.849.981 2.635.537.786 1.253 1.437 2.093 1.906M14.25 3.104c.739-.07 1.461.097 2.032.511a3.034 3.034 0 0 1 1.214 1.978c.059.482.036.965-.068 1.432-.208.928-.148 1.879.169 2.757a6.993 6.993 0 0 0 1.655 2.412 6.492 6.492 0 0 1 1.327 2.31 6.51 6.51 0 0 1 .297 2.724 6.894 6.894 0 0 1-.981 2.635c-.537.786-1.253 1.437-2.093 1.906m-5.552-4.297c.11-.314.234-.624.372-.928.551-1.214 1.339-2.293 2.328-3.184a7.805 7.805 0 0 0 1.71-2.428 7.83 7.83 0 0 0 .586-2.714"/>
                </svg>
                {{ __('Consult') }}
            </x-responsive-nav-link>
        </div>

        <!-- Mobile User Profile -->
        <div class="mt-auto border-t border-blue-600 p-4">
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-blue-800 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-blue-200">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="mt-4 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block text-blue-200 hover:text-white py-2 px-4 rounded-lg text-sm">
                    {{ __('Profile Settings') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-blue-200 hover:text-white py-2 px-4 rounded-lg text-sm">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Toggle Button -->
    <button @click="open = true" class="fixed top-4 left-4 inline-flex items-center justify-center p-2 rounded-md text-white bg-blue-700 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
</div>
