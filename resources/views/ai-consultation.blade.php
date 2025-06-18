<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fish Species Consultation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Consultation Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form id="consultationForm" class="space-y-4">
                        <div>
                            <label for="fish_species" class="block text-sm font-medium text-gray-700">Fish Species</label>
                            <select name="fish_species" id="fish_species" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="" disabled selected>Select a fish species</option>
                                @php
                                    $species = \App\Models\CatchAnalysis::query()
                                        ->distinct()
                                        ->pluck('fish_species')
                                        ->filter()
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach($species as $sp)
                                    <option value="{{ $sp }}">{{ $sp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Get Consultation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="hidden">
                <div class="flex justify-center items-center py-12">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Results Container -->
            <div id="consultationResults" class="hidden space-y-6">
                <!-- Expert Insights -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Expert Insights</h3>
                        <div id="expertInsights" class="prose max-w-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('consultationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const loadingIndicator = document.getElementById('loadingIndicator');
            const resultsContainer = document.getElementById('consultationResults');
            
            // Show loading indicator
            loadingIndicator.classList.remove('hidden');
            resultsContainer.classList.add('hidden');
            
            try {
                const formData = {
                    fish_species: document.getElementById('fish_species').value
                };
                
                const response = await fetch('/ai-consultation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to get consultation');
                }
                
                // Update Expert Insights
                const expertInsights = document.getElementById('expertInsights');
                expertInsights.innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Recommendations</h4>
                            <ul class="list-disc pl-5 mt-2">
                                ${data.expert_insights.recommendations.map(rec => `<li class="mb-2">${rec}</li>`).join('')}
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Success Patterns</h4>
                            <div class="mt-2">
                                ${Object.entries(data.expert_insights.success_patterns).map(([weather, stats]) => `
                                    <div class="mb-2">
                                        <p><strong>${weather}:</strong></p>
                                        <ul class="list-disc pl-5">
                                            <li>Success Rate: ${stats.success_rate.toFixed(1)}%</li>
                                            <li>Average Catch: ${stats.avg_quantity.toFixed(1)}</li>
                                        </ul>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Sustainability Tips</h4>
                            <ul class="list-disc pl-5 mt-2">
                                ${data.expert_insights.sustainability_tips.map(tip => `<li class="mb-2">${tip}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                `;
                
                // Show results
                resultsContainer.classList.remove('hidden');
            } catch (error) {
                alert(error.message);
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout> 