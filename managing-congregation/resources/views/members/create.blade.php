<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('members.index') }}" class="mr-4 text-slate-600 hover:text-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Create New Member') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:members.create-member />

        {{-- Help Text --}}
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">{{ __('Member Information Tips') }}</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li>{{ __('Civil names are the legal names given at birth') }}</li>
                        <li>{{ __('Religious name is chosen during formation (can be added later)') }}</li>
                        <li>{{ __('Entry date is when the member joined the congregation') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
