<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Profile') }}" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-xl border border-stone-200">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-xl border border-stone-200">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if(!app()->environment('production'))
                <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-xl border-2 border-amber-500">
                    <div class="max-w-xl">
                        @include('profile.partials.update-role-form')
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-xl border border-stone-200">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
