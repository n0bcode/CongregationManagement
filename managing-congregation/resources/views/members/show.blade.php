<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Member Profile') }}
            </h2>
            <div class="flex items-center space-x-4">
                @can('update', $member)
                    <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Edit') }}
                    </a>
                @endcan
                <a href="{{ route('members.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Timeline Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Formation Timeline') }}</h3>
                    <x-feast-timeline :events="$member->formationEvents" :member="$member" :projectedEvents="$projectedEvents" />
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Personal Information') }}</h3>
                            <dl class="mt-4 space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Full Name') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Religious Name') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->religious_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Date of Birth') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->dob->format('M d, Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Community Status') }}</h3>
                            <dl class="mt-4 space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $member->status }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Entry Date') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->entry_date->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Community') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->community->name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Formation Event Modal -->
    <x-modal name="add-formation-event" focusable>
        <form method="post" action="{{ route('members.formation.store', $member) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add Formation Milestone') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="stage" value="{{ __('Stage') }}" />
                <select id="stage" name="stage" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    @foreach(\App\Enums\FormationStage::cases() as $stage)
                        <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('stage')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="started_at" value="{{ __('Start Date') }}" />
                <x-text-input id="started_at" name="started_at" type="date" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('started_at')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="notes" value="{{ __('Notes (Optional)') }}" />
                <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Save Milestone') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
