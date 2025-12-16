<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header 
            title="{{ __('Create New Member') }}" 
            :backUrl="route('members.index')"
        />
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
