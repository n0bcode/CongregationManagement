<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Header & Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <h2 class="text-2xl font-bold text-gray-800">Financial Dashboard</h2>
                
                <div class="flex flex-wrap gap-4">
                    <!-- Year Filter -->
                    <select wire:model.live="year" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(range(now()->year, now()->year - 4) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>

                    <!-- Community Filter -->
                    <select wire:model.live="communityId" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($communities as $community)
                            <option value="{{ $community->id }}">{{ $community->name }}</option>
                        @endforeach
                    </select>

                    <!-- Project Filter -->
                    <select wire:model.live="projectId" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>

                    <!-- Export Buttons -->
                    <div class="flex space-x-2">
                        <button wire:click="export('csv')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            CSV
                        </button>
                        <button wire:click="export('pdf')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Monthly Expenses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Monthly Expenses</h3>
                <div class="relative h-64">
                    <canvas id="monthlyExpensesChart"></canvas>
                </div>
            </div>

            <!-- Expenses by Category -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Expenses by Category</h3>
                <div class="relative h-64">
                    <canvas id="categoryExpensesChart"></canvas>
                </div>
            </div>

            <!-- Budget vs Actual (Only if Project Selected) -->
            @if($projectId)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Budget vs Actual</h3>
                    <div class="relative h-64">
                        <canvas id="budgetVsActualChart"></canvas>
                    </div>
                </div>
            @endif
        </div>
    </div>



    <script>
        document.addEventListener('livewire:initialized', () => {
            let monthlyChart = null;
            let categoryChart = null;
            let budgetChart = null;

            const initCharts = () => {
                // Monthly Expenses Chart
                const monthlyCtx = document.getElementById('monthlyExpensesChart');
                if (monthlyCtx) {
                    if (monthlyChart) monthlyChart.destroy();
                    monthlyChart = new Chart(monthlyCtx, {
                        type: 'bar',
                        data: @json($monthlyExpenses),
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }

                // Category Expenses Chart
                const categoryCtx = document.getElementById('categoryExpensesChart');
                if (categoryCtx) {
                    if (categoryChart) categoryChart.destroy();
                    categoryChart = new Chart(categoryCtx, {
                        type: 'doughnut',
                        data: @json($expensesByCategory),
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }

                // Budget Chart
                const budgetCtx = document.getElementById('budgetVsActualChart');
                if (budgetCtx) {
                    if (budgetChart) budgetChart.destroy();
                    budgetChart = new Chart(budgetCtx, {
                        type: 'bar',
                        data: @json($budgetVsActual),
                        options: { 
                            indexAxis: 'y',
                            responsive: true, 
                            maintainAspectRatio: false 
                        }
                    });
                }
            };

            initCharts();

            // Re-init charts when Livewire updates
            Livewire.hook('morph.updated', ({ el, component }) => {
                initCharts();
            });
        });
    </script>
</div>
