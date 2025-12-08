<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Lock Financial Period') }}
            </h2>
            <x-button variant="secondary" href="{{ route('financials.index') }}">
                {{ __('Back to Financials') }}
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Info Alert --}}
        <x-alert type="info" class="mb-6">
            <div>
                <strong>{{ __('About Period Locking') }}</strong>
                <p class="text-sm mt-1">
                    {{ __('Locking a period prevents any expenses in that month from being edited or deleted. This ensures financial records remain accurate and tamper-proof for reporting and auditing purposes.') }}
                </p>
            </div>
        </x-alert>

        {{-- Selection Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8 mb-6">
            <h3 class="text-xl font-semibold text-slate-800 mb-6">{{ __('Select Period to Lock') }}</h3>
            
            <form method="GET" action="{{ route('financials.lock-period.form') }}" class="space-y-6">
                {{-- Community --}}
                <div>
                    <label for="community_id" class="block text-lg font-medium text-slate-800 mb-2">
                        {{ __('Community') }} <span class="text-rose-600">*</span>
                    </label>
                    <select 
                        name="community_id" 
                        id="community_id"
                        required
                        onchange="this.form.submit()"
                        class="w-full min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                    >
                        <option value="">{{ __('Select a community') }}</option>
                        @foreach($communities as $community)
                            <option value="{{ $community->id }}" {{ $communityId == $community->id ? 'selected' : '' }}>
                                {{ $community->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Year --}}
                    <div>
                        <label for="year" class="block text-lg font-medium text-slate-800 mb-2">
                            {{ __('Year') }} <span class="text-rose-600">*</span>
                        </label>
                        <select 
                            name="year" 
                            id="year"
                            required
                            onchange="this.form.submit()"
                            class="w-full min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                        >
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Month --}}
                    <div>
                        <label for="month" class="block text-lg font-medium text-slate-800 mb-2">
                            {{ __('Month') }} <span class="text-rose-600">*</span>
                        </label>
                        <select 
                            name="month" 
                            id="month"
                            required
                            onchange="this.form.submit()"
                            class="w-full min-h-[48px] px-4 py-3 text-base border border-stone-300 rounded-lg focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                        >
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        {{-- Lock Status --}}
        @if($lockStatus)
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8 mb-6">
                <h3 class="text-xl font-semibold text-slate-800 mb-6">
                    {{ __('Period Status') }}: {{ $lockStatus['period']['month_name'] }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <x-status-card 
                        variant="info"
                        :title="__('Total Expenses')"
                        :value="(string) $lockStatus['total_expenses']"
                    />
                    <x-status-card 
                        variant="attention"
                        :title="__('Locked')"
                        :value="(string) $lockStatus['locked_expenses']"
                    />
                    <x-status-card 
                        variant="pending"
                        :title="__('Unlocked')"
                        :value="(string) $lockStatus['unlocked_expenses']"
                    />
                </div>

                {{-- Progress Bar --}}
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-slate-600 mb-2">
                        <span>{{ __('Lock Progress') }}</span>
                        <span>{{ number_format($lockStatus['lock_percentage'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-stone-200 rounded-full h-4">
                        <div 
                            class="h-4 rounded-full transition-all {{ $lockStatus['is_fully_locked'] ? 'bg-emerald-600' : 'bg-amber-600' }}"
                            style="width: {{ $lockStatus['lock_percentage'] }}%"
                        ></div>
                    </div>
                </div>

                {{-- Status Message --}}
                @if($lockStatus['is_fully_locked'])
                    <x-alert type="success">
                        <strong>{{ __('Period Fully Locked') }}</strong>
                        <p class="text-sm mt-1">
                            {{ __('All expenses in this period are locked and cannot be modified.') }}
                        </p>
                    </x-alert>
                @elseif($lockStatus['is_partially_locked'])
                    <x-alert type="warning" class="mb-4">
                        <strong>{{ __('Period Partially Locked') }}</strong>
                        <p class="text-sm mt-1">
                            {{ __('Some expenses in this period are locked. You can lock the remaining expenses below.') }}
                        </p>
                    </x-alert>
                @else
                    <x-alert type="info" class="mb-4">
                        <strong>{{ __('Period Not Locked') }}</strong>
                        <p class="text-sm mt-1">
                            {{ __('No expenses in this period are locked. You can lock all expenses below.') }}
                        </p>
                    </x-alert>
                @endif

                {{-- Lock Action --}}
                @if(!$lockStatus['is_fully_locked'] && $lockStatus['unlocked_expenses'] > 0)
                    <form method="POST" action="{{ route('financials.lock-period') }}" class="mt-6">
                        @csrf
                        <input type="hidden" name="community_id" value="{{ $communityId }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="month" value="{{ $month }}">

                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-amber-600 mr-3 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="flex-grow">
                                    <h4 class="font-semibold text-amber-900 mb-2">{{ __('Warning: This action cannot be undone') }}</h4>
                                    <p class="text-sm text-amber-800 mb-4">
                                        {{ __('Locking this period will prevent all') }} <strong>{{ $lockStatus['unlocked_expenses'] }}</strong> 
                                        {{ __('unlocked expense(s) from being edited or deleted. Make sure all expenses are accurate before proceeding.') }}
                                    </p>
                                    <x-button 
                                        type="submit" 
                                        variant="danger"
                                        onclick="return confirm('{{ __('Are you sure you want to lock this period? This action cannot be undone.') }}')"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        {{ __('Lock Period') }}
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
