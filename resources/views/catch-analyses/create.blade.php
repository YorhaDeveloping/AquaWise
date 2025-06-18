<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Catch Record') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">New Catch Analysis</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Submit your catch details for analysis. Our experts will review and provide insights.
                    </p>
                </div>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <form action="{{ route('catch-analyses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <!-- Fish Species -->
                            <div>
                                <label for="fish_species" class="block text-sm font-medium text-gray-700">Fish Species</label>
                                <div class="mt-1">
                                    <input type="text" name="fish_species" id="fish_species" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('fish_species') }}" required>
                                </div>
                                @error('fish_species')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity (number of fish)</label>
                                <div class="mt-1">
                                    <input type="number" min="1" name="quantity" id="quantity" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('quantity') }}" required>
                                </div>
                                @error('quantity')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Weight -->
                            <div>
                                <label for="total_weight" class="block text-sm font-medium text-gray-700">Total Weight (kg)</label>
                                <div class="mt-1">
                                    <input type="number" step="0.01" min="0" name="total_weight" id="total_weight" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('total_weight') }}" required>
                                </div>
                                @error('total_weight')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Average Size -->
                            <div>
                                <label for="average_size" class="block text-sm font-medium text-gray-700">Average Size (kg)</label>
                                <div class="mt-1">
                                    <input type="number" step="0.01" min="0" name="average_size" id="average_size" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('average_size') }}">
                                </div>
                                @error('average_size')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Catch Location</label>
                                <div class="mt-1">
                                    <input type="text" name="location" id="location" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('location') }}" required>
                                </div>
                                @error('location')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Weather Conditions -->
                            <div>
                                <label for="weather_conditions" class="block text-sm font-medium text-gray-700">Weather Conditions</label>
                                <div class="mt-1">
                                    <select name="weather_conditions" id="weather_conditions" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Select weather condition</option>
                                        <option value="Clear" {{ old('weather_conditions') == 'Clear' ? 'selected' : '' }}>Clear</option>
                                        <option value="Partly Cloudy" {{ old('weather_conditions') == 'Partly Cloudy' ? 'selected' : '' }}>Partly Cloudy</option>
                                        <option value="Cloudy" {{ old('weather_conditions') == 'Cloudy' ? 'selected' : '' }}>Cloudy</option>
                                        <option value="Light Rain" {{ old('weather_conditions') == 'Light Rain' ? 'selected' : '' }}>Light Rain</option>
                                        <option value="Moderate Rain" {{ old('weather_conditions') == 'Moderate Rain' ? 'selected' : '' }}>Moderate Rain</option>
                                        <option value="Heavy Rain" {{ old('weather_conditions') == 'Heavy Rain' ? 'selected' : '' }}>Heavy Rain</option>
                                        <option value="Thunderstorm" {{ old('weather_conditions') == 'Thunderstorm' ? 'selected' : '' }}>Thunderstorm</option>
                                    </select>
                                </div>
                                @error('weather_conditions')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date -->
                            <div>
                                <label for="catch_date" class="block text-sm font-medium text-gray-700">Catch Date</label>
                                <div class="mt-1">
                                    <input type="date" name="catch_date" id="catch_date" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('catch_date') }}" required>
                                </div>
                                @error('catch_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                <div class="mt-1">
                                    <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('notes') }}</textarea>
                                </div>
                                @error('notes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catch Photo</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <img id="preview" src="#" alt="Preview" class="mt-2 max-h-48 hidden">
                                        </div>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload a file</span>
                                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <a href="{{ route('catch-analyses.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Submit Analysis
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout> 