<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header 
            title="{{ __('Edit Member') }}" 
            :backUrl="route('members.show', $member)"
        />
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

                {{-- Passport Information --}}
                <div class="pt-6 border-t border-stone-200">
                    <h3 class="text-lg font-medium text-slate-700 mb-4">{{ __('Passport & Identification') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="passport_number" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Passport Number') }}
                            </label>
                            <input 
                                type="text" 
                                name="passport_number" 
                                id="passport_number"
                                value="{{ old('passport_number', $member->passport_number) }}"
                                placeholder="{{ __('Enter passport number...') }}"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('passport_number') border-rose-500 @enderror"
                            >
                            @error('passport_number')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passport_place_of_issue" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Place of Issue') }}
                            </label>
                            <input 
                                type="text" 
                                name="passport_place_of_issue" 
                                id="passport_place_of_issue"
                                value="{{ old('passport_place_of_issue', $member->passport_place_of_issue) }}"
                                placeholder="{{ __('Enter place of issue...') }}"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('passport_place_of_issue') border-rose-500 @enderror"
                            >
                            @error('passport_place_of_issue')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passport_issued_at" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Date of Issue') }}
                            </label>
                            <input 
                                type="date" 
                                name="passport_issued_at" 
                                id="passport_issued_at"
                                value="{{ old('passport_issued_at', $member->passport_issued_at?->format('Y-m-d')) }}"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('passport_issued_at') border-rose-500 @enderror"
                            >
                            @error('passport_issued_at')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passport_expired_at" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Date of Expiry') }}
                            </label>
                            <input 
                                type="date" 
                                name="passport_expired_at" 
                                id="passport_expired_at"
                                value="{{ old('passport_expired_at', $member->passport_expired_at?->format('Y-m-d')) }}"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('passport_expired_at') border-rose-500 @enderror"
                            >
                            @error('passport_expired_at')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
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
                    <x-ui.button type="submit" variant="primary" class="flex-1">
                        {{ __('Update Member') }}
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('members.show', $member) }}" class="flex-1">
                        {{ __('Cancel') }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
