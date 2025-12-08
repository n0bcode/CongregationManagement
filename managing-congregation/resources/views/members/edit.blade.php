<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('members.show', $member) }}" class="mr-4 text-slate-600 hover:text-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Edit Member') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
            <form method="POST" action="{{ route('members.update', $member) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Name Fields (Civil) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-lg font-medium text-slate-700 mb-2">
                            {{ __('First Name (Civil)') }} <span class="text-rose-600">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="first_name" 
                            id="first_name"
                            value="{{ old('first_name', $member->first_name) }}"
                            required
                            placeholder="{{ __('Enter first name...') }}"
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('first_name') border-rose-500 @enderror"
                        >
                        @error('first_name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-lg font-medium text-slate-700 mb-2">
                            {{ __('Last Name (Civil)') }} <span class="text-rose-600">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="last_name" 
                            id="last_name"
                            value="{{ old('last_name', $member->last_name) }}"
                            required
                            placeholder="{{ __('Enter last name...') }}"
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('last_name') border-rose-500 @enderror"
                        >
                        @error('last_name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Religious Name --}}
                <div>
                    <label for="religious_name" class="block text-lg font-medium text-slate-700 mb-2">
                        {{ __('Religious Name') }} <span class="text-slate-500 text-sm font-normal">({{ __('Optional') }})</span>
                    </label>
                    <input 
                        type="text" 
                        name="religious_name" 
                        id="religious_name"
                        value="{{ old('religious_name', $member->religious_name) }}"
                        placeholder="{{ __('Enter religious name...') }}"
                        class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('religious_name') border-rose-500 @enderror"
                    >
                    @error('religious_name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Important Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="dob" class="block text-lg font-medium text-slate-700 mb-2">
                            {{ __('Date of Birth') }} <span class="text-rose-600">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="dob" 
                            id="dob"
                            value="{{ old('dob', $member->dob->format('Y-m-d')) }}"
                            required
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('dob') border-rose-500 @enderror"
                        >
                        @error('dob')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="entry_date" class="block text-lg font-medium text-slate-700 mb-2">
                            {{ __('Entry Date') }} <span class="text-rose-600">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="entry_date" 
                            id="entry_date"
                            value="{{ old('entry_date', $member->entry_date->format('Y-m-d')) }}"
                            required
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('entry_date') border-rose-500 @enderror"
                        >
                        @error('entry_date')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-lg font-medium text-slate-700 mb-2">
                        {{ __('Status') }} <span class="text-rose-600">*</span>
                    </label>
                    <select 
                        name="status" 
                        id="status"
                        required
                        class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('status') border-rose-500 @enderror"
                    >
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ old('status', $member->status) === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form Actions --}}
                <div class="flex gap-4 pt-6 border-t border-stone-200">
                    <x-button type="submit" variant="primary" class="flex-1">
                        {{ __('Update Member') }}
                    </x-button>
                    <x-button variant="secondary" href="{{ route('members.show', $member) }}" class="flex-1">
                        {{ __('Cancel') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
