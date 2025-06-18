<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AquaWise') }}</title>

    <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('logo/aquawise.png') }}">

    <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: (window.innerWidth >= 768), isMobile: window.innerWidth < 768 }" 
             x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 768 })"
             class="min-h-screen bg-gray-100">
            
            <!-- Sidebar Overlay -->
            <div x-show="sidebarOpen && isMobile" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20"
                 @click="sidebarOpen = false"></div>

            <!-- Sidebar -->
            <div x-cloak
                 :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
                 class="fixed inset-y-0 left-0 z-30 w-64 bg-blue-700 transform transition-transform duration-300 ease-in-out overflow-y-auto">
                
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-4 bg-blue-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <span class="text-2xl font-bold text-white">AquaWise</span>
                    </a>
                    <!-- Close button for mobile -->
                    <button x-show="isMobile" @click="sidebarOpen = false" class="text-white hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                </button>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-5 px-2 space-y-1">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-4 py-2 text-base font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('catch-analyses.index') }}" 
                       class="flex items-center px-4 py-2 text-base font-medium rounded-lg {{ request()->routeIs('catch-analyses.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Catch Analysis
                    </a>

                    <a href="{{ route('fish-guides.index') }}" 
                       class="flex items-center px-4 py-2 text-base font-medium rounded-lg {{ request()->routeIs('fish-guides.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Fish Guides
                    </a>

                    <a href="{{ route('weather.index') }}" 
                       class="flex items-center px-4 py-2 text-base font-medium rounded-lg {{ request()->routeIs('weather.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                        Weather Forecast
                    </a>

                    <a href="{{ route('ai.consultation.index') }}" 
                       class="flex items-center px-4 py-2 text-base font-medium rounded-lg {{ request()->routeIs('ai.consultation.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-600' }}">
                        <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8m-8 4h5m1 6l-3-3H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2h-1v3z"/>
                        </svg>
                        AI Consultation
                    </a>
                </nav>

                <!-- User Profile -->
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

            <!-- Main Content -->
            <div :class="{'pl-64': sidebarOpen && !isMobile}"
                 class="transition-padding duration-300 ease-in-out">
                <!-- Top Navigation -->
                <div class="bg-white shadow">
                    <div class="flex justify-between items-center px-4 py-3">
                        <div>
                            <!-- Toggle Sidebar Button -->
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                                <span class="sr-only">Toggle sidebar</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Page Title -->
                        <div class="flex-1 px-4 text-center sm:text-left">
                            <h1 class="text-xl font-semibold text-gray-900">
                                @yield('title', 'AquaWise')
                            </h1>
                        </div>

                        <!-- User Menu (Desktop) -->
                        <div class="hidden sm:flex sm:items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                            <span class="text-sm font-medium text-gray-600">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                        <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();"
                                                class="flex items-center">
                                            <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                                </div>
                </div>

                <!-- Page Content -->
                <main class="py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
    </div>
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
