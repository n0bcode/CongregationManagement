<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Financial Dashboard') }}" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:financial-dashboard />
        </div>
    </div>
</x-app-layout>
