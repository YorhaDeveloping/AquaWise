<x-app-layout>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Catch Analysis Records</h2>
                <a href="{{ route('catch-analyses.create') }}" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                    Add New Record
                </a>
            </div>

            <!-- Charts Section -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 sm:mb-0">
                        @if(auth()->user()->hasRole('expert'))
                            All Catch Analytics
                        @else
                            My Catch Analytics
                        @endif
                    </h3>
                    <div class="flex space-x-4">
                        <select id="timeFilter" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="all">All Time</option>
                            <option value="week">Last Week</option>
                            <option value="month">Last Month</option>
                            <option value="year">Last Year</option>
                        </select>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-sm font-medium text-gray-500">Total Catch</h3>
                        <div class="mt-2 flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900" id="totalCatch">-</p>
                            <p class="ml-2 text-sm font-medium text-gray-500">fish</p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-sm font-medium text-gray-500">Average Weight per Catch</h3>
                        <div class="mt-2 flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900" id="avgWeight">-</p>
                            <p class="ml-2 text-sm font-medium text-gray-500">kg/fish</p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-sm font-medium text-gray-500">Total Weight</h3>
                        <div class="mt-2 flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900" id="totalWeight">-</p>
                            <p class="ml-2 text-sm font-medium text-gray-500">kg</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Catch Trends Chart -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Catch & Weight Trends</h3>
                        <canvas id="catchTrendsChart"></canvas>
                    </div>
                    <!-- Average Size Trend Chart -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Average Size Trend</h3>
                        <canvas id="avgSizeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Initialize Charts -->
            @push('scripts')
            <script>
                function filterData(data, period) {
                    if (period === 'all') return data;
                    
                    const now = new Date();
                    const periods = {
                        'week': 7,
                        'month': 30,
                        'year': 365
                    };
                    const cutoff = new Date(now - (periods[period] * 24 * 60 * 60 * 1000));
                    
                    return data.filter(item => new Date(item.date) > cutoff);
                }

                // Prepare data
                @php
                    $query = $catchAnalyses;
                    if (!auth()->user()->hasRole('expert')) {
                        $query = $query->where('user_id', auth()->id());
                    }
                    $chartData = $query->sortBy('catch_date')->values()->map(function($analysis) {
                        return [
                            'date' => $analysis->catch_date->format('M d, Y'),
                            'weight' => $analysis->total_weight,
                            'quantity' => $analysis->quantity,
                            'avgSize' => $analysis->average_size
                        ];
                    });
                @endphp

                const allData = {!! json_encode($chartData) !!};

                function updateCharts(period) {
                    const filteredData = filterData(allData, period);
                    
                    // Update summary cards
                    const totals = filteredData.reduce((acc, item) => {
                        acc.totalCatch += item.quantity;
                        acc.totalWeight += item.weight;
                        return acc;
                    }, { totalCatch: 0, totalWeight: 0 });
                    
                    const avgWeight = totals.totalCatch > 0 ? (totals.totalWeight / totals.totalCatch).toFixed(2) : 0;
                    
                    document.getElementById('totalCatch').textContent = totals.totalCatch;
                    document.getElementById('avgWeight').textContent = avgWeight;
                    document.getElementById('totalWeight').textContent = totals.totalWeight.toFixed(2);

                    // Update catch trends chart
                    catchTrendsChart.data.labels = filteredData.map(item => item.date);
                    catchTrendsChart.data.datasets[0].data = filteredData.map(item => item.quantity);
                    catchTrendsChart.data.datasets[1].data = filteredData.map(item => item.weight);
                    catchTrendsChart.update();

                    // Update average size chart
                    avgSizeChart.data.labels = filteredData.map(item => item.date);
                    avgSizeChart.data.datasets[0].data = filteredData.map(item => item.avgSize);
                    avgSizeChart.update();
                }

                // Initialize catch trends chart
                const catchTrendsChart = new Chart(document.getElementById('catchTrendsChart'), {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Quantity',
                            data: [],
                            borderColor: '#2563EB',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            yAxisID: 'y',
                            tension: 0.4
                        }, {
                            label: 'Weight (kg)',
                            data: [],
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            fill: true,
                            yAxisID: 'y1',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Quantity'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Weight (kg)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            }
                        }
                    }
                });

                // Initialize average size chart
                const avgSizeChart = new Chart(document.getElementById('avgSizeChart'), {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Average Size',
                            data: [],
                            borderColor: '#DC2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Average Size'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            }
                        }
                    }
                });

                // Initialize with all data
                window.addEventListener('load', function() {
                    updateCharts('all');
                });

                // Add event listener for time filter
                document.getElementById('timeFilter').addEventListener('change', function(e) {
                    updateCharts(e.target.value);
                });
            </script>
            @endpush

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fish Species</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Weight</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($catchAnalyses as $analysis)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $analysis->catch_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $analysis->fish_species }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $analysis->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $analysis->total_weight }} kg
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $analysis->location }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('catch-analyses.show', $analysis) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    @can('update', $analysis)
                                        @if(!$analysis->expertReviews->count())
                                            <a href="{{ route('catch-analyses.edit', $analysis) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        @else
                                            <span class="text-gray-400 mr-3" title="Cannot edit: Analysis has expert reviews">Edit</span>
                                        @endif
                                    @endcan
                                    @can('delete', $analysis)
                                        @if(!$analysis->expertReviews->count())
                                            <form action="{{ route('catch-analyses.destroy', $analysis) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this record?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400" title="Cannot delete: Analysis has expert reviews">Delete</span>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No catch analysis records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden">
                <div class="space-y-4">
                    @forelse ($catchAnalyses as $analysis)
                        <div class="bg-white shadow rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-900">{{ $analysis->catch_date->format('M d, Y') }}</span>
                                <span class="text-sm font-medium text-gray-500">{{ $analysis->fish_species }}</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Quantity:</span>
                                    <span class="text-sm text-gray-900">{{ $analysis->quantity }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Weight:</span>
                                    <span class="text-sm text-gray-900">{{ $analysis->total_weight }} kg</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Location:</span>
                                    <span class="text-sm text-gray-900">{{ $analysis->location }}</span>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-3">
                                <a href="{{ route('catch-analyses.show', $analysis) }}" class="text-blue-600 hover:text-blue-900 text-sm">View</a>
                                @can('update', $analysis)
                                    @if(!$analysis->expertReviews->count())
                                        <a href="{{ route('catch-analyses.edit', $analysis) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                                    @else
                                        <span class="text-gray-400 text-sm" title="Cannot edit: Analysis has expert reviews">Edit</span>
                                    @endif
                                @endcan
                                @can('delete', $analysis)
                                    @if(!$analysis->expertReviews->count())
                                        <form action="{{ route('catch-analyses.destroy', $analysis) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-sm" title="Cannot delete: Analysis has expert reviews">Delete</span>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">
                            No catch analysis records found.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $catchAnalyses->links() }}
            </div>
        </div>
    </div>
</x-app-layout> 