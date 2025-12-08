<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white/50 dark:bg-gray-800/50 shadow-sm sm:rounded-xl backdrop-blur-sm border border-stone-200/50">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white/50 dark:bg-gray-800/50 shadow-sm sm:rounded-xl backdrop-blur-sm border border-stone-200/50">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if(!app()->environment('production'))
                <div class="p-4 sm:p-8 bg-white/50 dark:bg-gray-800/50 shadow-sm sm:rounded-xl backdrop-blur-sm border-2 border-amber-500/50">
                    <div class="max-w-xl">
                        @include('profile.partials.update-role-form')
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white/50 dark:bg-gray-800/50 shadow-sm sm:rounded-xl backdrop-blur-sm border border-stone-200/50">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
