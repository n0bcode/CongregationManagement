<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Financial Records') }}
            </h2>
            <div class="flex gap-3">
                @php
                    $defaultCommunityId = auth()->user()->community_id ?? \App\Models\Community::first()?->id ?? 1;
                @endphp
                <x-ui.button variant="ghost" href="{{ route('financials.monthly-report', ['community_id' => $defaultCommunityId, 'year' => now()->year, 'month' => now()->month]) }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    {{ __('Reports') }}
                </x-ui.button>
                @can('lockPeriod', \App\Models\Expense::class)
                    <x-ui.button variant="secondary" href="{{ route('financials.lock-period.form') }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ __('Lock Period') }}
                    </x-ui.button>
                @endcan
                @can('create', \App\Models\Expense::class)
                    <x-ui.button variant="primary" href="{{ route('financials.create') }}">
                        {{ __('Record Expense') }}
                    </x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6 mb-6">
            <form method="GET" action="{{ route('financials.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Category Filter --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-2">
                            {{ __('Category') }}
                        </label>
                        <select 
                            name="category" 
                            id="category"
                            class="w-full min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                        >
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Month Filter --}}
                    <div>
                        <label for="month" class="block text-sm font-medium text-slate-700 mb-2">
                            {{ __('Month') }}
                        </label>
                        <input 
                            type="month" 
                            name="month" 
                            id="month"
                            value="{{ request('month') }}"
                            class="w-full min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                        >
                    </div>

                    {{-- Date Range --}}
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            {{ __('Date Range') }}
                        </label>
                        <div class="flex gap-2">
                            <input 
                                type="date" 
                                name="start_date" 
                                value="{{ request('start_date') }}"
                                placeholder="Start"
                                class="flex-1 min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                            >
                            <input 
                                type="date" 
                                name="end_date" 
                                value="{{ request('end_date') }}"
                                placeholder="End"
                                class="flex-1 min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <x-ui.button type="submit" variant="primary">
                        {{ __('Apply Filters') }}
                    </x-ui.button>
                    @if(request()->hasAny(['category', 'month', 'start_date', 'end_date']))
                        <x-ui.button variant="secondary" href="{{ route('financials.index') }}">
                            {{ __('Clear Filters') }}
                        </x-ui.button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Summary Card --}}
        <div class="mb-6">
            <x-ui.status-card 
                variant="info" 
                title="{{ __('Total Expenses') }}"
                value="${{ number_format($totalAmount / 100, 2) }}"
                :description="__('For the selected period')"
            />
        </div>

        {{-- Expenses List --}}
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden">
            @if($expenses->count() > 0)
                <div class="divide-y divide-stone-200">
                    @foreach($expenses as $expense)
                        <x-features.ledger-row
                            :date="$expense->date"
                            :description="$expense->description"
                            :category="$expense->category"
                            :amount="'$' . number_format($expense->amount / 100, 2)"
                            :href="route('financials.show', $expense)"
                        >
                            <div class="flex items-center gap-4 text-sm">
                                <span class="text-slate-600">
                                    {{ $expense->community->name }}
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

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-stone-200">
                <x-ui.pagination :paginator="$expenses" />
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-slate-900">{{ __('No expenses found') }}</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        @if(request()->hasAny(['category', 'month', 'start_date', 'end_date']))
                            {{ __('Try adjusting your filters or') }}
                            <a href="{{ route('financials.index') }}" class="text-amber-600 hover:text-amber-700 font-medium">
                                {{ __('clear all filters') }}
                            </a>
                        @else
                            {{ __('Get started by recording your first expense.') }}
                        @endif
                    </p>
                    <div class="mt-6">
                        <x-ui.button variant="primary" href="{{ route('financials.create') }}">
                            {{ __('Record Expense') }}
                        </x-ui.button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
