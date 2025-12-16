<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Members') }}
            </h2>
            <x-ui.button variant="primary" href="{{ route('members.create') }}">
                {{ __('Create Member') }}
            </x-ui.button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:members-table />
    </div>
</x-app-layout>
