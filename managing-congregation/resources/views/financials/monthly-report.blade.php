<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Monthly Financial Report') }}">
            <x-slot:actions>
                <x-ui.button variant="secondary" href="{{ route('financials.index') }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back') }}
                </x-ui.button>
                <x-ui.button 
                    variant="secondary" 
                    href="{{ route('financials.export-report', ['community_id' => $report['community_id'], 'year' => $report['period']['year'], 'month' => $report['period']['month']]) }}"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('Export PDF') }}
                </x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Period Selector --}}
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6 mb-6">
            <form method="GET" action="{{ route('financials.monthly-report') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="community_id" class="block text-sm font-medium text-slate-700 mb-2">
                            {{ __('Community') }}
                        </label>
                        <select 
                            name="community_id" 
                            id="community_id"
                            required
                            class="w-full min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                        >
                            @foreach($communities as $community)
                                <option value="{{ $community->id }}" {{ $report['community_id'] == $community->id ? 'selected' : '' }}>
                                    {{ $community->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-medium text-slate-700 mb-2">
                            {{ __('Year') }}
                        </label>
                        <select 
                            name="year" 
                            id="year"
                            required
                            class="w-full min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                        >
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $report['period']['year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="month" class="block text-sm font-medium text-slate-700 mb-2">
                            {{ __('Month') }}
                        </label>
                        <select 
                            name="month" 
                            id="month"
                            required
                            class="w-full min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                        >
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $report['period']['month'] == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <x-ui.button type="submit" variant="primary">
                    {{ __('Generate Report') }}
                </x-ui.button>
            </form>
        </div>

        {{-- Report Header --}}
        <div class="bg-gradient-to-r from-amber-50 to-stone-50 rounded-lg shadow-sm border border-stone-200 p-8 mb-6">
            <h3 class="text-2xl font-bold text-slate-800 mb-2">{{ $report['period']['month_name'] }}</h3>
            <p class="text-slate-600">{{ __('Financial Summary Report') }}</p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-ui.status-card 
                variant="info"
                :title="__('Total Expenses')"
                :value="'$' . number_format($report['summary']['total_amount_dollars'], 2)"
                :description="__('For the selected period')"
            />
            <x-ui.status-card 
                variant="pending"
                :title="__('Transactions')"
                :value="(string) $report['summary']['total_count']"
                :description="__('Number of expenses recorded')"
            />
            <x-ui.status-card 
                variant="peace"
                :title="__('Average Expense')"
                :value="'$' . number_format($report['summary']['average_expense_dollars'], 2)"
                :description="__('Per transaction')"
            />
        </div>

        {{-- Expenses by Category --}}
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8 mb-6">
            <h3 class="text-xl font-semibold text-slate-800 mb-6">{{ __('Expenses by Category') }}</h3>
            
            @if($report['by_category']->count() > 0)
                <div class="space-y-6">
                    @foreach($report['by_category'] as $category)
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <span class="font-medium text-slate-800">{{ $category['category'] }}</span>
                                <span class="text-sm text-slate-500 ml-2">({{ $category['count'] }} {{ __('transactions') }})</span>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-slate-900">${{ number_format($category['total_dollars'], 2) }}</div>
                                <div class="text-sm text-slate-500">{{ number_format($category['percentage'], 1) }}% {{ __('of total') }}</div>
                            </div>
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="w-full bg-stone-200 rounded-full h-3">
                            <div 
                                class="bg-amber-600 h-3 rounded-full transition-all"
                                style="width: {{ $category['percentage'] }}%"
                            ></div>
                        </div>
                        
                        <div class="mt-2 text-sm text-slate-600">
                            {{ __('Average') }}: ${{ number_format($category['average_dollars'], 2) }} {{ __('per transaction') }}
                        </div>
                    </div>
                @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-slate-900">{{ __('No expenses recorded') }}</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        {{ __('There are no expenses for this period. Start by recording your first expense.') }}
                    </p>
                    @can('create', \App\Models\Expense::class)
                        <div class="mt-6">
                            <x-ui.button variant="primary" href="{{ route('financials.create') }}">
                                {{ __('Record Expense') }}
                            </x-ui.button>
                        </div>
                    @endcan
                </div>
            @endif
        </div>

        {{-- Daily Breakdown --}}
        @if($report['daily_breakdown']->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8 mb-6">
                <h3 class="text-xl font-semibold text-slate-800 mb-6">{{ __('Daily Breakdown') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    {{ __('Date') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    {{ __('Transactions') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    {{ __('Total') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-stone-200">
                            @foreach($report['daily_breakdown'] as $day)
                                <tr class="hover:bg-stone-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                        {{ \Carbon\Carbon::parse($day['date'])->format('l, F j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-slate-900">
                                        {{ $day['count'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-slate-900">
                                        ${{ number_format($day['total'] / 100, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Detailed Expense List --}}
        @if($report['expenses']->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-stone-200">
                    <h3 class="text-xl font-semibold text-slate-800">{{ __('Detailed Expense List') }}</h3>
                </div>
                
                <div class="divide-y divide-stone-200">
                    @foreach($report['expenses'] as $expense)
                    <x-features.ledger-row
                        :date="$expense->date"
                        :description="$expense->description"
                        :category="$expense->category"
                        :amount="'$' . number_format($expense->amount / 100, 2)"
                        :href="route('financials.show', $expense)"
                    >
                        <div class="flex items-center gap-4 text-sm">
                            <span class="text-slate-600">
                                {{ __('Created by') }}: {{ $expense->creator->name }}
                            </span>
                            @if($expense->is_locked)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    {{ __('Locked') }}
                                </span>
                            @endif
                            @if($expense->receipt_path)
                                <span class="inline-flex items-center text-amber-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    {{ __('Receipt') }}
                                </span>
                            @endif
                        </div>
                    </x-features.ledger-row>
                @endforeach
                </div>
            </div>
        @endif

        {{-- Report Footer --}}
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-center">
            <p class="text-sm text-blue-800">
                {{ __('Report generated on') }} {{ now()->format('F j, Y \a\t g:i A') }}
            </p>
        </div>
    </div>
</x-app-layout>
