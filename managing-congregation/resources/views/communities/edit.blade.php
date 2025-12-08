<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('Edit Community') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
                <form method="POST" action="{{ route('communities.update', $community) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Community Name') }} <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $community->name) }}"
                                   required
                                   autofocus
                                   class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('name') border-rose-500 @enderror"
                                   placeholder="{{ __('Enter community name') }}">
                            @error('name')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location Field -->
                        <div>
                            <label for="location" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Location') }}
                            </label>
                            <input type="text" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location', $community->location) }}"
                                   class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('location') border-rose-500 @enderror"
                                   placeholder="{{ __('Enter location (optional)') }}">
                            @error('location')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-slate-500">
                                {{ __('City, province, or address of the community') }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-4 pt-6 border-t border-stone-200">
                            <button type="submit" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500 transition-all shadow-sm hover:shadow-md flex-1">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('Update Community') }}
                            </button>
                            <a href="{{ route('communities.show', $community) }}" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 bg-white text-slate-700 font-semibold rounded-lg border-2 border-stone-300 hover:border-stone-400 focus:outline-none focus:ring-4 focus:ring-stone-300 transition-all flex-1">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Community Info -->
            <div class="mt-6 bg-stone-50 border border-stone-200 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-slate-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-base font-semibold text-slate-800 mb-2">{{ __('Community Details') }}</h3>
                        <dl class="space-y-2 text-sm text-slate-600">
                            <div class="flex">
                                <dt class="font-medium w-32">{{ __('Members:') }}</dt>
                                <dd>{{ $community->members()->count() }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="font-medium w-32">{{ __('Created:') }}</dt>
                                <dd>{{ $community->created_at->format('F d, Y') }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="font-medium w-32">{{ __('Last Updated:') }}</dt>
                                <dd>{{ $community->updated_at->format('F d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
