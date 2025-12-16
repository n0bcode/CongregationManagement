<x-forms.form-with-unsaved-warning>
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
        <form wire:submit.prevent="save" x-data="smartForm({ totalSteps: 3 })" @submit="$el.closest('[x-data]').markAsSaved()">
            
            {{-- Step 1: Personal Information --}}
            <x-forms.wizard-step step="1">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Step 1: Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-forms.validated-input 
                        name="first_name" 
                        label="First Name (Civil)" 
                        wire:model.live="first_name" 
                        rules="required|string|max:255"
                    />

                    <x-forms.validated-input 
                        name="last_name" 
                        label="Last Name (Civil)" 
                        wire:model.live="last_name" 
                        rules="required|string|max:255"
                    />
                </div>

                <div class="mt-4">
                    <x-forms.validated-input 
                        name="dob" 
                        label="Date of Birth" 
                        type="date"
                        wire:model.live="dob" 
                        rules="required|date|before:today"
                    />
                </div>

                <div class="mt-6 border-t border-gray-200 pt-4">
                    <h4 class="text-md font-medium text-gray-700 mb-3">Passport Information (Optional)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-forms.validated-input 
                            name="passport_number" 
                            label="Passport Number" 
                            wire:model.live="passport_number" 
                            rules="nullable|string|max:50"
                        />
                        <x-forms.validated-input 
                            name="passport_place_of_issue" 
                            label="Place of Issue" 
                            wire:model.live="passport_place_of_issue" 
                            rules="nullable|string|max:255"
                        />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <x-forms.validated-input 
                            name="passport_issued_at" 
                            label="Date of Issue" 
                            type="date"
                            wire:model.live="passport_issued_at" 
                            rules="nullable|date"
                        />
                        <x-forms.validated-input 
                            name="passport_expired_at" 
                            label="Date of Expiry" 
                            type="date"
                            wire:model.live="passport_expired_at" 
                            rules="nullable|date|after:passport_issued_at"
                        />
                    </div>
                </div>
            </x-forms.wizard-step>

            {{-- Step 2: Religious Information --}}
            <x-forms.wizard-step step="2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Step 2: Religious Information</h3>
                
                <x-forms.validated-input 
                    name="religious_name" 
                    label="Religious Name (Optional)" 
                    wire:model.live="religious_name" 
                    rules="nullable|string|max:255"
                />

                <div class="mt-4" x-data="{ memberType: @entangle('member_type') }">
                    <x-ui.label for="member_type" value="Member Type" required />
                    
                    <x-ui.select 
                        id="member_type" 
                        x-model="memberType"
                        wire:model.live="member_type" 
                        class="mt-1"
                    >
                        <option value="postulant">Postulant</option>
                        <option value="novice">Novice</option>
                        <option value="professed">Professed</option>
                    </x-ui.select>
                    @error('member_type') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror

                    <div class="mt-4">
                        <x-forms.validated-input 
                            name="entry_date" 
                            label="Entry Date" 
                            type="date"
                            wire:model.live="entry_date" 
                            rules="required|date"
                        />
                    </div>

                    {{-- Conditional Formation Dates --}}
                    <div x-show="memberType === 'novice' || memberType === 'professed'" class="mt-6">
                        <x-ui.alert variant="warning" title="Formation Dates">
                            
                            <x-forms.validated-input 
                                name="novitiate_entry_date" 
                                label="Novitiate Entry Date" 
                                type="date"
                                wire:model.live="novitiate_entry_date" 
                                ::rules="memberType === 'novice' || memberType === 'professed' ? 'required|date|after:entry_date' : ''"
                            />

                            <div x-show="memberType === 'professed'" class="mt-4">
                                <x-forms.validated-input 
                                    name="first_vows_date" 
                                    label="First Vows Date" 
                                    type="date"
                                    wire:model.live="first_vows_date" 
                                    ::rules="memberType === 'professed' ? 'required|date|after:novitiate_entry_date' : ''"
                                />
                            </div>

                            <div x-show="memberType === 'professed'" class="mt-4">
                                <x-forms.validated-input 
                                    name="perpetual_vows_date" 
                                    label="Perpetual Vows Date (Optional)" 
                                    type="date"
                                    wire:model.live="perpetual_vows_date" 
                                    rules="nullable|date|after:first_vows_date"
                                />
                            </div>
                        </x-ui.alert>
                    </div>
                </div>
            </x-forms.wizard-step>

            {{-- Step 3: Community --}}
            <x-forms.wizard-step step="3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Step 3: Community Assignment</h3>

                @if(auth()->user()->community_id === null)
                    <div>
                        <x-ui.label for="community_id" value="Community" required />
                        
                        <x-ui.select 
                            id="community_id" 
                            wire:model.live="community_id" 
                            class="mt-1"
                        >
                            <option value="">Select a community...</option>
                            @foreach($communities as $community)
                                <option value="{{ $community->id }}">{{ $community->name }}</option>
                            @endforeach
                        </x-ui.select>
                        @error('community_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                @else
                    <x-ui.alert variant="info">
                        You are creating this member in your assigned community.
                    </x-ui.alert>
                @endif
            </x-forms.wizard-step>

            <x-forms.wizard-navigation submitLabel="Create Member" />
        </form>
    </div>
</x-forms.form-with-unsaved-warning>
