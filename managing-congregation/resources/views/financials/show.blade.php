<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('financials.index') }}" class="mr-4 text-slate-600 hover:text-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="text-3xl font-bold text-stone-800">
                    {{ __('Expense Details') }}
                </h2>
            </div>
            @if(!$expense->is_locked)
                @can('update', $expense)
                    <div class="flex gap-3">
                        <x-button variant="secondary" href="{{ route('financials.edit', $expense) }}">
                            {{ __('Edit') }}
                        </x-button>
                        @can('delete', $expense)
                            <form method="POST" action="{{ route('financials.destroy', $expense) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this expense?') }}');">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="danger">
                                    {{ __('Delete') }}
                                </x-button>
                            </form>
                        @endcan
                    </div>
                @endcan
            @endif
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Locked Status Alert --}}
        @if($expense->is_locked)
            <x-alert type="warning" class="mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div>
                        <strong>{{ __('This expense is locked') }}</strong>
                        <p class="text-sm mt-1">
                            {{ __('Locked on') }} {{ $expense->locked_at->format('F j, Y') }}
                            @if($expense->locker)
                                {{ __('by') }} {{ $expense->locker->name }}
                            @endif
                        </p>
                    </div>
                </div>
            </x-alert>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden">
            {{-- Header with Amount --}}
            <div class="bg-gradient-to-r from-amber-50 to-stone-50 px-8 py-6 border-b border-stone-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600 mb-1">{{ __('Total Amount') }}</p>
                        <p class="text-4xl font-bold text-slate-900">${{ number_format($expense->amount / 100, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-600 mb-1">{{ __('Date') }}</p>
                        <p class="text-3xl font-bold text-stone-800">{{ $expense->date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="p-8 space-y-6">
                {{-- Community --}}
                <div>
                    <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-2">
                        {{ __('Community') }}
                    </h3>
                    <p class="text-lg text-slate-900">{{ $expense->community->name }}</p>
                </div>

                {{-- Category --}}
                <div>
                    <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-2">
                        {{ __('Category') }}
                    </h3>
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-base font-medium bg-amber-100 text-amber-800">
                        {{ $expense->category }}
                    </span>
                </div>

                {{-- Description --}}
                <div>
                    <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-2">
                        {{ __('Description') }}
                    </h3>
                    <p class="text-lg text-slate-900 whitespace-pre-wrap">{{ $expense->description }}</p>
                </div>

                {{-- Receipt --}}
                @if($expense->receipt_path)
                    <div>
                        <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-2">
                            {{ __('Receipt') }}
                        </h3>
                        <a 
                            href="{{ asset('storage/' . $expense->receipt_path) }}" 
                            target="_blank"
                            class="inline-flex items-center px-4 py-3 bg-stone-100 hover:bg-stone-200 text-slate-800 rounded-lg transition-colors min-h-[48px]"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            {{ __('View Receipt') }}
                        </a>
                    </div>
                @endif

                {{-- Metadata --}}
                <div class="pt-6 border-t border-stone-200">
                    <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-3">
                        {{ __('Record Information') }}
                    </h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-slate-600">{{ __('Created By') }}</dt>
                            <dd class="text-base text-slate-900 mt-1">{{ $expense->creator->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-slate-600">{{ __('Created At') }}</dt>
                            <dd class="text-base text-slate-900 mt-1">{{ $expense->created_at->format('M d, Y g:i A') }}</dd>
                        </div>
                        @if($expense->updated_at != $expense->created_at)
                            <div>
                                <dt class="text-sm text-slate-600">{{ __('Last Updated') }}</dt>
                                <dd class="text-base text-slate-900 mt-1">{{ $expense->updated_at->format('M d, Y g:i A') }}</dd>
                            </div>
                        @endif
                        @if($expense->is_locked && $expense->locker)
                            <div>
                                <dt class="text-sm text-slate-600">{{ __('Locked By') }}</dt>
                                <dd class="text-base text-slate-900 mt-1">{{ $expense->locker->name }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex gap-4">
            <x-button variant="secondary" href="{{ route('financials.index') }}" class="flex-1">
                {{ __('Back to List') }}
            </x-button>
            @if(!$expense->is_locked)
                <x-button variant="primary" href="{{ route('financials.edit', $expense) }}" class="flex-1">
                    {{ __('Edit Expense') }}
                </x-button>
            @endif
        </div>
    </div>
</x-app-layout>
