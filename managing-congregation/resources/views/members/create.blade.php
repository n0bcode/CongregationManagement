<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <x-ui.button variant="ghost" href="{{ route('members.index') }}" class="mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </x-ui.button>
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Create New Member') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:members.create-member />

        {{-- Help Text --}}
        <div class="mt-6">
            <x-ui.alert variant="info" title="{{ __('Member Information Tips') }}">
                <ul class="list-disc list-inside space-y-1">
                    <li>{{ __('Civil names are the legal names given at birth') }}</li>
                    <li>{{ __('Religious name is chosen during formation (can be added later)') }}</li>
                    <li>{{ __('Entry date is when the member joined the congregation') }}</li>
                </ul>
            </x-ui.alert>
        </div>
    </div>
</x-app-layout>
