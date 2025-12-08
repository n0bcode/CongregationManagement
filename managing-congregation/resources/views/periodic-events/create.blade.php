<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('Create Periodic Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('periodic-events.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <x-forms.input-label for="name" :value="__('Event Name')" />
                            <x-forms.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-forms.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-forms.input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('description') }}</textarea>
                            <x-forms.input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Start Date -->
                            <div class="mb-4">
                                <x-forms.input-label for="start_date" :value="__('Start Date')" />
                                <x-forms.text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                                <x-forms.input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <!-- End Date -->
                            <div class="mb-4">
                                <x-forms.input-label for="end_date" :value="__('End Date')" />
                                <x-forms.text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" required />
                                <x-forms.input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Recurrence -->
                            <div class="mb-4">
                                <x-forms.input-label for="recurrence" :value="__('Recurrence')" />
                                <select id="recurrence" name="recurrence" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="one-time" {{ old('recurrence') == 'one-time' ? 'selected' : '' }}>One-time</option>
                                    <option value="annual" {{ old('recurrence') == 'annual' ? 'selected' : '' }}>Annual</option>
                                    <option value="monthly" {{ old('recurrence') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                <x-forms.input-error :messages="$errors->get('recurrence')" class="mt-2" />
                            </div>

                            <!-- Community -->
                            <div class="mb-4">
                                <x-forms.input-label for="community_id" :value="__('Community (Optional)')" />
                                <select id="community_id" name="community_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Communities</option>
                                    @foreach ($communities as $community)
                                        <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                            {{ $community->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-forms.input-error :messages="$errors->get('community_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('periodic-events.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-ui.primary-button>
                                {{ __('Create Event') }}
                            </x-ui.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
