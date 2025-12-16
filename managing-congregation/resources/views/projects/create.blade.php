<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Create Project') }}" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <x-forms.input-label for="name" :value="__('Project Name')" />
                            <x-forms.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-forms.input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-forms.input-label for="description" :value="__('Description')" />
                            <x-forms.textarea-input id="description" name="description" class="block mt-1 w-full" rows="4">{{ old('description') }}</x-forms.textarea-input>
                            <x-forms.input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Community -->
                        <div class="mb-4">
                            <x-forms.input-label for="community_id" :value="__('Community')" />
                            <x-forms.select-input id="community_id" name="community_id" class="block mt-1 w-full" required>
                                <option value="">Select Community</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </x-forms.select-input>
                            <x-forms.input-error :messages="$errors->get('community_id')" class="mt-2" />
                        </div>

                        <!-- Manager -->
                        <div class="mb-4">
                            <x-forms.input-label for="manager_id" :value="__('Project Manager')" />
                            <x-forms.select-input id="manager_id" name="manager_id" class="block mt-1 w-full">
                                <option value="">Select Manager (Optional)</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('manager_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->first_name }} {{ $member->last_name }}
                                    </option>
                                @endforeach
                            </x-forms.select-input>
                            <x-forms.input-error :messages="$errors->get('manager_id')" class="mt-2" />
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-forms.input-label for="start_date" :value="__('Start Date')" />
                                <x-forms.text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" />
                                <x-forms.input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-forms.input-label for="end_date" :value="__('End Date')" />
                                <x-forms.text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" />
                                <x-forms.input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Status & Budget -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-forms.input-label for="status" :value="__('Status')" />
                                <x-forms.select-input id="status" name="status" class="block mt-1 w-full" required>
                                    <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </x-forms.select-input>
                                <x-forms.input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                            <div>
                                <x-forms.input-label for="budget" :value="__('Budget')" />
                                <x-forms.text-input id="budget" class="block mt-1 w-full" type="number" step="0.01" name="budget" :value="old('budget', 0)" required />
                                <x-forms.input-error :messages="$errors->get('budget')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-ui.primary-button>
                                {{ __('Create Project') }}
                            </x-ui.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
