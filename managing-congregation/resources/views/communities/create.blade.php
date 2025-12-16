<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('Create New Community') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
                <form method="POST" action="{{ route('communities.store') }}">
                    @csrf

                    <div class="space-y-6">
                        <!-- Name Field -->
                        <div>
                            <x-ui.label for="name" :value="__('Community Name')" :required="true" />
                            <x-ui.input 
                                id="name" 
                                name="name" 
                                type="text"
                                :value="old('name')" 
                                :error="$errors->first('name')"
                                required 
                                autofocus 
                                placeholder="{{ __('Enter community name') }}" 
                            />
                        </div>

                        <!-- Location Field -->
                        <div>
                            <x-ui.label for="location" :value="__('Location')" />
                            <x-ui.input 
                                id="location" 
                                name="location" 
                                type="text"
                                :value="old('location')" 
                                :error="$errors->first('location')"
                                placeholder="{{ __('Enter location (optional)') }}" 
                            />
                            <p class="mt-2 text-sm text-slate-500">
                                {{ __('City, province, or address of the community') }}
                            </p>
                        </div>

                        <!-- Patron Saint Field -->
                        <div>
                            <x-ui.label for="patron_saint" :value="__('Patron Saint')" />
                            <x-ui.input 
                                id="patron_saint" 
                                name="patron_saint" 
                                type="text"
                                :value="old('patron_saint')" 
                                :error="$errors->first('patron_saint')"
                                placeholder="{{ __('Enter Patron Saint') }}" 
                            />
                        </div>

                        <!-- Foundation Date & Feast Day -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-ui.label for="founded_at" :value="__('Foundation Date')" />
                                <x-ui.input 
                                    id="founded_at" 
                                    name="founded_at" 
                                    type="date"
                                    :value="old('founded_at')" 
                                    :error="$errors->first('founded_at')"
                                />
                            </div>

                            <div>
                                <x-ui.label for="feast_day" :value="__('Feast Day')" />
                                <x-ui.input 
                                    id="feast_day" 
                                    name="feast_day" 
                                    type="date"
                                    :value="old('feast_day')" 
                                    :error="$errors->first('feast_day')"
                                />
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-ui.label for="email" :value="__('Email')" />
                                <x-ui.input 
                                    id="email" 
                                    name="email" 
                                    type="email"
                                    :value="old('email')" 
                                    :error="$errors->first('email')"
                                    placeholder="email@example.com" 
                                />
                            </div>

                            <div>
                                <x-ui.label for="phone" :value="__('Phone')" />
                                <x-ui.input 
                                    id="phone" 
                                    name="phone" 
                                    type="text"
                                    :value="old('phone')" 
                                    :error="$errors->first('phone')"
                                    placeholder="{{ __('Phone number') }}" 
                                />
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-4 pt-6 border-t border-stone-200">
                            <button type="submit" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500 transition-all shadow-sm hover:shadow-md flex-1">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('Create Community') }}
                            </button>
                            <a href="{{ route('communities.index') }}" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 bg-white text-slate-700 font-semibold rounded-lg border-2 border-stone-300 hover:border-stone-400 focus:outline-none focus:ring-4 focus:ring-stone-300 transition-all flex-1">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Help Text -->
            <div class="mt-6 bg-amber-50 border border-amber-200 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-amber-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-base font-semibold text-slate-800 mb-2">{{ __('About Communities') }}</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ __('Communities represent different congregations or groups within your organization. Each member belongs to one community, and certain roles (like Directors) can only manage members within their own community.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
