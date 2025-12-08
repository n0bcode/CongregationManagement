<x-form-with-unsaved-warning>
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
        <form wire:submit.prevent="save" x-data="smartForm({ totalSteps: 3 })" @submit="$el.closest('[x-data]').markAsSaved()">
            
            {{-- Step 1: Personal Information --}}
            <x-wizard-step step="1">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Step 1: Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-validated-input 
                        name="first_name" 
                        label="First Name (Civil)" 
                        wire:model.live="first_name" 
                        rules="required|string|max:255"
                    />

                    <x-validated-input 
                        name="last_name" 
                        label="Last Name (Civil)" 
                        wire:model.live="last_name" 
                        rules="required|string|max:255"
                    />
                </div>

                <div class="mt-4">
                    <x-validated-input 
                        name="dob" 
                        label="Date of Birth" 
                        type="date"
                        wire:model.live="dob" 
                        rules="required|date|before:today"
                    />
                </div>
            </x-wizard-step>

            {{-- Step 2: Religious Information --}}
            <x-wizard-step step="2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Step 2: Religious Information</h3>
                
                <x-validated-input 
                    name="religious_name" 
                    label="Religious Name (Optional)" 
                    wire:model.live="religious_name" 
                    rules="nullable|string|max:255"
                />

                <div class="mt-4" x-data="{ memberType: @entangle('member_type') }">
                    <label for="member_type" class="block font-medium text-sm text-gray-700 mb-1">
                        Member Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="member_type" 
                        x-model="memberType"
                        wire:model.live="member_type" 
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
                    >
                        <option value="postulant">Postulant</option>
                        <option value="novice">Novice</option>
                        <option value="professed">Professed</option>
                    </select>
                    @error('member_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

                    <div class="mt-4">
                        <x-validated-input 
                            name="entry_date" 
                            label="Entry Date" 
                            type="date"
                            wire:model.live="entry_date" 
                            rules="required|date"
                        />
                    </div>

                    {{-- Conditional Formation Dates --}}
                    <div x-show="memberType === 'novice' || memberType === 'professed'" class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                        <h4 class="font-medium text-amber-900 mb-3">Formation Dates</h4>
                        
                        <x-validated-input 
                            name="novitiate_entry_date" 
                            label="Novitiate Entry Date" 
                            type="date"
                            wire:model.live="novitiate_entry_date" 
                            ::rules="memberType === 'novice' || memberType === 'professed' ? 'required|date|after:entry_date' : ''"
                        />

                        <div x-show="memberType === 'professed'" class="mt-4">
                            <x-validated-input 
                                name="first_vows_date" 
                                label="First Vows Date" 
                                type="date"
                                wire:model.live="first_vows_date" 
                                ::rules="memberType === 'professed' ? 'required|date|after:novitiate_entry_date' : ''"
                            />
                        </div>

                        <div x-show="memberType === 'professed'" class="mt-4">
                            <x-validated-input 
                                name="perpetual_vows_date" 
                                label="Perpetual Vows Date (Optional)" 
                                type="date"
                                wire:model.live="perpetual_vows_date" 
                                rules="nullable|date|after:first_vows_date"
                            />
                        </div>
                    </div>
                </div>
            </x-wizard-step>

            {{-- Step 3: Community --}}
            <x-wizard-step step="3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Step 3: Community Assignment</h3>

                @if(auth()->user()->community_id === null)
                    <div>
                        <label for="community_id" class="block font-medium text-sm text-gray-700 mb-1">
                            Community <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="community_id" 
                            wire:model.live="community_id" 
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
                        >
                            <option value="">Select a community...</option>
                            @foreach($communities as $community)
                                <option value="{{ $community->id }}">{{ $community->name }}</option>
                            @endforeach
                        </select>
                        @error('community_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div class="p-4 bg-blue-50 text-blue-700 rounded-md">
                        You are creating this member in your assigned community.
                    </div>
                @endif
            </x-wizard-step>

            <x-wizard-navigation submitLabel="Create Member" />
        </form>
    </div>
</x-form-with-unsaved-warning>
