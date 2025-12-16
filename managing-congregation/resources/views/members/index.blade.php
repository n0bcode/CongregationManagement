<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Members') }}">
            <x-slot:actions>
                <x-ui.button variant="primary" href="{{ route('members.create') }}">
                    {{ __('Create Member') }}
                </x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:members-table />
    </div>
</x-app-layout>
