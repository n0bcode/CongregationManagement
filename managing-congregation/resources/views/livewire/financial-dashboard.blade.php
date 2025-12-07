<div>
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="community" class="block text-sm font-medium text-gray-700">Community</label>
                <select wire:model.live="communityId" id="community" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($communities as $community)
                        <option value="{{ $community->id }}">{{ $community->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                <select wire:model.live="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                <select wire:model.live="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Total Expenses</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">${{ number_format($report['summary']['total_amount_dollars'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Transaction Count</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $report['summary']['total_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Average Expense</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">${{ number_format($report['summary']['average_expense_dollars'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Status</h3>
            <div class="mt-2">
                @php
                    $lockStatus = app(\App\Services\FinancialService::class)->getPeriodLockStatus($communityId, $year, $month);
                @endphp
                @if($lockStatus['is_fully_locked'])
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Locked
                    </span>
                @elseif($lockStatus['is_partially_locked'])
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Partially Locked
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Open
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Expense Trend</h3>
            <div class="relative h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Category Distribution</h3>
            <div class="relative h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($report['expenses']->take(10) as $expense)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $expense->date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $expense->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ${{ number_format($expense->amount / 100, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($expense->is_locked)
                                    <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No transactions found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($report['expenses']->count() > 10)
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('financials.index', ['month' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT), 'community_id' => $communityId]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    View all transactions &rarr;
                </a>
            </div>
        @endif
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let trendChart = null;
            let categoryChart = null;

            const initCharts = (trendData, categoryData) => {
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                const categoryCtx = document.getElementById('categoryChart').getContext('2d');

                if (trendChart) trendChart.destroy();
                if (categoryChart) categoryChart.destroy();

                trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: trendData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value;
                                    }
                                }
                            }
                        }
                    }
                });

                categoryChart = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: categoryData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            };

            // Initial render
            initCharts(@json($trendChartData), @json($categoryChartData));

            // Update on Livewire updates
            Livewire.hook('morph.updated', ({ component, el }) => {
                // We need to re-fetch the data from the component properties
                // This is a bit tricky with Livewire 3, so we might need to emit an event from PHP
            });
            
            Livewire.on('charts-updated', (data) => {
                 initCharts(data.trend, data.category);
            });
        });
    </script>
</div>
