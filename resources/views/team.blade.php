<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AquaWise - Meet the Team</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                           <a href="/"> <span class="text-2xl font-bold text-blue-600">AquaWise</span></a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if (Route::has('login'))
                            <div class="space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Log in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Register</a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Team Section -->
        <div class="flex-grow py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        Meet Our Team
                    </h2>
                    <p class="mt-4 text-lg text-gray-500">
                        The passionate people behind AquaWise
                    </p>
                </div>
                <div class="mt-10">
                    <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Team Member 2 -->
                        <div class="bg-white overflow-hidden shadow rounded-lg flex flex-col items-center p-6">
                            <img class="h-24 w-24 rounded-full object-cover" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Team member 2">
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Almira Estrella</h3>
                            <p class="text-blue-600 font-semibold">Support Developer</p>
                            <p class="mt-2 text-base text-gray-500 text-center">
                            </p>
                        </div>
                        <!-- Team Member 1 -->
                        <div class="bg-white overflow-hidden shadow rounded-lg flex flex-col items-center p-6">
                            <img class="h-24 w-24 rounded-full object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Team member 1">
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Meynard Torda</h3>
                            <p class="text-blue-600 font-semibold">Lead Developer</p>
                            <p class="mt-2 text-base text-gray-500 text-center">
                                Frontend Developer and Backend Developer
                            </p>
                        </div>
                        <!-- Team Member 3 -->
                        <div class="bg-white overflow-hidden shadow rounded-lg flex flex-col items-center p-6">
                            <img class="h-24 w-24 rounded-full object-cover" src="https://randomuser.me/api/portraits/women/65.jpg" alt="Team member 3">
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Mary Rose Enera</h3>
                            <p class="text-blue-600 font-semibold">Support Developer</p>
                            <p class="mt-2 text-base text-gray-500 text-center">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-center space-x-4">
                    <span class="text-gray-400 hover:text-gray-500">
                        © {{ date('Y') }} AquaWise. All rights reserved.
                    </span>
                    <span class="mx-2">|</span>
                    <span>
                        <a href="{{ route('team') }}" class="text-gray-700 hover:text-gray-900 font-semibold">Our Team</a>
                    </span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
