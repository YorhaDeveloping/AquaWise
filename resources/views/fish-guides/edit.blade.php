<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Fish Guide') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('fish-guides.update', $guide) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $guide->title)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="fish_species" :value="__('Fish Species')" />
                            <x-text-input id="fish_species" name="fish_species" type="text" class="mt-1 block w-full" :value="old('fish_species', $guide->fish_species)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('fish_species')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" required>{{ old('description', $guide->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="care_instructions" :value="__('Care Instructions')" />
                            <textarea id="care_instructions" name="care_instructions" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" required>{{ old('care_instructions', $guide->care_instructions) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('care_instructions')" />
                        </div>

                        <div>
                            <x-input-label for="feeding_guide" :value="__('Feeding Guide')" />
                            <textarea id="feeding_guide" name="feeding_guide" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" required>{{ old('feeding_guide', $guide->feeding_guide) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('feeding_guide')" />
                        </div>

                        <div class="space-y-4">
                            <x-input-label :value="__('Water Parameters')" />
                            <div>
                                <x-input-label for="water_parameters_temperature" :value="__('Temperature Range')" />
                                <x-text-input id="water_parameters_temperature" name="water_parameters[temperature]" type="text" class="mt-1 block w-full" :value="old('water_parameters.temperature', $guide->water_parameters['temperature'] ?? '')" required placeholder="e.g., 22-26°C" />
                                <x-input-error class="mt-2" :messages="$errors->get('water_parameters.temperature')" />
                            </div>
                            <div>
                                <x-input-label for="water_parameters_ph" :value="__('pH Range')" />
                                <x-text-input id="water_parameters_ph" name="water_parameters[ph]" type="text" class="mt-1 block w-full" :value="old('water_parameters.ph', $guide->water_parameters['ph'] ?? '')" required placeholder="e.g., 6.5-7.5" />
                                <x-input-error class="mt-2" :messages="$errors->get('water_parameters.ph')" />
                            </div>
                            <div>
                                <x-input-label for="water_parameters_hardness" :value="__('Water Hardness')" />
                                <x-text-input id="water_parameters_hardness" name="water_parameters[hardness]" type="text" class="mt-1 block w-full" :value="old('water_parameters.hardness', $guide->water_parameters['hardness'] ?? '')" required placeholder="e.g., 5-12 dKH" />
                                <x-input-error class="mt-2" :messages="$errors->get('water_parameters.hardness')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="common_diseases" :value="__('Common Diseases')" />
                            <textarea id="common_diseases" name="common_diseases" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('common_diseases', $guide->common_diseases) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('common_diseases')" />
                        </div>

                        <div>
                            <x-input-label for="prevention_tips" :value="__('Prevention Tips')" />
                            <textarea id="prevention_tips" name="prevention_tips" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('prevention_tips', $guide->prevention_tips) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('prevention_tips')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="draft" {{ old('status', $guide->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $guide->status) == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button onclick="window.history.back()" type="button" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Guide') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>