<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Fish Guide') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('fish-guides.store') }}" class="space-y-6" id="guideForm">
                        @csrf
                        
                        <input type="hidden" name="ai_assisted" id="ai_assisted" value="0">
                        <input type="hidden" name="confidence_score" id="confidence_score" value="">
                        <input type="hidden" name="expert_review_count" id="expert_review_count" value="">
                        <input type="hidden" name="catch_analysis_count" id="catch_analysis_count" value="">

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="relative">
                            <x-input-label for="fish_species" :value="__('Fish Species')" />
                            <x-text-input id="fish_species" name="fish_species" type="text" class="mt-1 block w-full" :value="old('fish_species')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('fish_species')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" required>{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="care_instructions" :value="__('Care Instructions')" />
                            <textarea id="care_instructions" name="care_instructions" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" required>{{ old('care_instructions') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('care_instructions')" />
                        </div>

                        <div>
                            <x-input-label for="feeding_guide" :value="__('Feeding Guide')" />
                            <textarea id="feeding_guide" name="feeding_guide" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" required>{{ old('feeding_guide') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('feeding_guide')" />
                        </div>

                        <div class="space-y-4">
                            <x-input-label :value="__('Water Parameters')" />
                            
                            <div>
                                <x-input-label for="water_parameters_temperature" :value="__('Temperature Range')" />
                                <x-text-input id="water_parameters_temperature" name="water_parameters[temperature]" type="text" class="mt-1 block w-full" :value="old('water_parameters.temperature')" required placeholder="e.g., 22-26°C" />
                                <x-input-error class="mt-2" :messages="$errors->get('water_parameters.temperature')" />
                            </div>

                            <div>
                                <x-input-label for="water_parameters_ph" :value="__('pH Range')" />
                                <x-text-input id="water_parameters_ph" name="water_parameters[ph]" type="text" class="mt-1 block w-full" :value="old('water_parameters.ph')" required placeholder="e.g., 6.5-7.5" />
                                <x-input-error class="mt-2" :messages="$errors->get('water_parameters.ph')" />
                            </div>

                            <div>
                                <x-input-label for="water_parameters_hardness" :value="__('Water Hardness')" />
                                <x-text-input id="water_parameters_hardness" name="water_parameters[hardness]" type="text" class="mt-1 block w-full" :value="old('water_parameters.hardness')" required placeholder="e.g., 5-12 dKH" />
                                <x-input-error class="mt-2" :messages="$errors->get('water_parameters.hardness')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="common_diseases" :value="__('Common Diseases')" />
                            <textarea id="common_diseases" name="common_diseases" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('common_diseases') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('common_diseases')" />
                        </div>

                        <div>
                            <x-input-label for="prevention_tips" :value="__('Prevention Tips')" />
                            <textarea id="prevention_tips" name="prevention_tips" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('prevention_tips') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('prevention_tips')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button onclick="window.history.back()" type="button" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Create Guide') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fetchButton = document.getElementById('fetchSuggestions');
            const form = document.getElementById('guideForm');
            const aiConfidence = document.getElementById('aiConfidence');
            const confidenceScore = document.getElementById('confidenceScore');
            const confidenceBar = document.getElementById('confidenceBar');
            const expertCount = document.getElementById('expertCount');
            const analysisCount = document.getElementById('analysisCount');

            // Setup axios defaults
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
            axios.defaults.headers.common['Accept'] = 'application/json';

            fetchButton.addEventListener('click', async function() {
                const fishSpecies = document.getElementById('fish_species').value;
                
                if (!fishSpecies) {
                    alert('Please enter a fish species first');
                    return;
                }

                try {
                    fetchButton.disabled = true;
                    fetchButton.textContent = 'Fetching...';
                    aiConfidence.classList.add('hidden');

                    const response = await axios.post('/fish-guides/suggestions', {
                        fish_species: fishSpecies
                    });

                    if (response.data.success) {
                        const data = response.data.data;
                        
                        // Update form fields with suggestions
                        document.getElementById('care_instructions').value = data.care_instructions.content || '';
                        document.getElementById('feeding_guide').value = data.feeding_guide.content || '';
                        
                        // Update water parameters
                        document.getElementById('water_parameters_temperature').value = data.water_parameters.temperature || '';
                        document.getElementById('water_parameters_ph').value = data.water_parameters.ph || '';
                        document.getElementById('water_parameters_hardness').value = data.water_parameters.hardness || '';
                        
                        document.getElementById('common_diseases').value = data.common_diseases.content || '';
                        document.getElementById('prevention_tips').value = data.prevention_tips.content || '';

                        // Update AI metadata
                        document.getElementById('ai_assisted').value = '1';
                        document.getElementById('confidence_score').value = data.confidence_score;
                        document.getElementById('expert_review_count').value = data.care_instructions.source_count.expert_reviews;
                        document.getElementById('catch_analysis_count').value = data.care_instructions.source_count.catch_analyses;

                        // Update confidence indicators
                        confidenceScore.textContent = `${data.confidence_score}%`;
                        confidenceBar.style.width = `${data.confidence_score}%`;
                        expertCount.textContent = data.care_instructions.source_count.expert_reviews;
                        analysisCount.textContent = data.care_instructions.source_count.catch_analyses;

                        aiConfidence.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to fetch suggestions. Please try again.');
                } finally {
                    fetchButton.disabled = false;
                    fetchButton.textContent = 'Get AI Suggestions';
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 