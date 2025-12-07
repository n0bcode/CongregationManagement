<div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
    <form wire:submit="save" class="space-y-6">
        {{-- Name Fields (Civil) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-lg font-medium text-slate-700 mb-2">
                    {{ __('First Name (Civil)') }} <span class="text-rose-600">*</span>
                </label>
                <input 
                    type="text" 
                    wire:model.live="first_name" 
                    id="first_name"
                    required
                    autofocus
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
                    wire:model.live="last_name" 
                    id="last_name"
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
                wire:model.live="religious_name" 
                id="religious_name"
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
                    wire:model.live="dob" 
                    id="dob"
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
                    wire:model.live="entry_date" 
                    id="entry_date"
                    required
                    class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('entry_date') border-rose-500 @enderror"
                >
                @error('entry_date')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Community Selection (Super Admin only) --}}
        @if(auth()->user()->community_id === null)
            <div>
                <label for="community_id" class="block text-lg font-medium text-slate-700 mb-2">
                    {{ __('Community') }} <span class="text-rose-600">*</span>
                </label>
                <select 
                    wire:model.live="community_id" 
                    id="community_id" 
                    required
                    class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('community_id') border-rose-500 @enderror"
                >
                    <option value="">{{ __('Select a community...') }}</option>
                    @foreach($communities as $community)
                        <option value="{{ $community->id }}">
                            {{ $community->name }}
                        </option>
                    @endforeach
                </select>
                @error('community_id')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        {{-- Form Actions --}}
        <div class="flex gap-4 pt-6 border-t border-stone-200">
            <x-button type="submit" variant="primary" class="flex-1" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Create Member') }}</span>
                <span wire:loading>{{ __('Creating...') }}</span>
            </x-button>
            <x-button variant="secondary" href="{{ route('members.index') }}" class="flex-1">
                {{ __('Cancel') }}
            </x-button>
        </div>
    </form>
</div>
