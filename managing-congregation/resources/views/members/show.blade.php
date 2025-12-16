<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header 
            title="{{ __('Member Profile') }}" 
            :backUrl="route('members.index')"
        >
            <x-slot:actions>
                <x-features.contextual-actions :model="$member" layout="buttons" />
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Member Header with Photo -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <img class="h-32 w-32 object-cover rounded-full border-4 border-stone-200" src="{{ $member->profile_photo_url }}" alt="{{ $member->full_name }}" />
                        </div>
                        <div class="flex-1">
                            <h2 class="text-3xl font-serif font-bold text-stone-800">
                                {{ $member->first_name }} {{ $member->last_name }}
                            </h2>
                            @if($member->religious_name)
                                <p class="text-xl text-slate-600 mt-1">{{ $member->religious_name }}</p>
                            @endif
                            <div class="mt-3 flex items-center space-x-4">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-sanctuary-green/10 text-sanctuary-green">
                                    {{ $member->status }}
                                </span>
                                @if($member->community)
                                    <a href="{{ route('communities.show', $member->community) }}" class="text-sm text-amber-600 hover:text-amber-700 hover:underline font-medium">
                                        {{ $member->community->name }}
                                    </a>
                                @else
                                    <span class="text-sm text-slate-600">N/A</span>
                                @endif
                            </div>
                        </div>
                        @can('update', $member)
                            <div class="flex flex-col space-y-2">
                                <x-ui.secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'update-photo')">
                                    {{ __('Update Photo') }}
                                </x-ui.secondary-button>
                                @if($member->profile_photo_path)
                                    <form method="POST" action="{{ route('members.photo.destroy', $member) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.danger-button onclick="return confirm('Are you sure you want to remove this photo?')" class="w-full">
                                            {{ __('Remove Photo') }}
                                        </x-ui.danger-button>
                                    </form>
                                @endif
                            </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Related Records Tabs -->
            <x-features.related-records :tabs="[
                'personal' => 'Personal',
                'formation' => 'Formation',
                'health' => 'Health',
                'skills' => 'Skills',
                'service' => 'Service History',
                'history' => 'Audit Log'
            ]">
                <x-slot name="personal">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-slate-900 mb-4">{{ __('Personal Information') }}</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">{{ __('Full Name') }}</dt>
                                    <dd class="mt-1 text-lg text-slate-900">{{ $member->first_name }} {{ $member->last_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">{{ __('Religious Name') }}</dt>
                                    <dd class="mt-1 text-lg text-slate-900">{{ $member->religious_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">{{ __('Date of Birth') }}</dt>
                                    <dd class="mt-1 text-lg text-slate-900">{{ $member->dob->format('M d, Y') }} ({{ $member->dob->age }} years old)</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-slate-900 mb-4">{{ __('Community Status') }}</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">{{ __('Status') }}</dt>
                                    <dd class="mt-1">
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-sanctuary-green/10 text-sanctuary-green">
                                            {{ $member->status }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">{{ __('Entry Date') }}</dt>
                                    <dd class="mt-1 text-lg text-slate-900">{{ $member->entry_date->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">{{ __('Community') }}</dt>
                                    <dd class="mt-1 text-lg text-slate-900">
                                        @if($member->community)
                                            <a href="{{ route('communities.show', $member->community) }}" class="text-amber-600 hover:text-amber-700 hover:underline font-medium">
                                                {{ $member->community->name }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </dd>
                                </div>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    {{-- Passport Information --}}
                    @if($member->passport_number)
                        <div class="mt-8 pt-6 border-t border-slate-200">
                            <h3 class="text-lg font-medium text-slate-900 mb-4">{{ __('Passport & Identification') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-slate-500">{{ __('Passport Number') }}</dt>
                                            <dd class="mt-1 text-lg text-slate-900 font-mono">{{ $member->passport_number }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-500">{{ __('Place of Issue') }}</dt>
                                            <dd class="mt-1 text-lg text-slate-900">{{ $member->passport_place_of_issue ?? '-' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-slate-500">{{ __('Issued Date') }}</dt>
                                            <dd class="mt-1 text-lg text-slate-900">{{ $member->passport_issued_at?->format('M d, Y') ?? '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-500">{{ __('Expiry Date') }}</dt>
                                            <dd class="mt-1 text-lg text-slate-900">
                                                {{ $member->passport_expired_at?->format('M d, Y') ?? '-' }}
                                                @if($member->passport_expired_at && $member->passport_expired_at->isPast())
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ __('Expired') }}
                                                    </span>
                                                @elseif($member->passport_expired_at && $member->passport_expired_at->diffInMonths(now()) < 6)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                        {{ __('Expiring Soon') }}
                                                    </span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    @endif
                </x-slot>

                <x-slot name="formation">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Formation Timeline') }}</h3>
                        @can('create', \App\Models\FormationEvent::class)
                            <x-ui.primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-formation-event')">
                                {{ __('Add Milestone') }}
                            </x-ui.primary-button>
                        @endcan
                    </div>
                    <x-features.formation-timeline :events="$member->formationEvents" :projectedEvents="$projectedEvents" />
                </x-slot>

                <x-slot name="health">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Health Records') }}</h3>
                        @can('update', $member)
                            <x-ui.primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-health-record')">
                                {{ __('Add Health Record') }}
                            </x-ui.primary-button>
                        @endcan
                    </div>
                    @if($member->healthRecords->count() > 0)
                        <div class="space-y-4">
                            @foreach($member->healthRecords as $record)
                                <div class="border border-stone-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-slate-900">{{ $record->condition }}</h4>
                                            @if($record->medications)
                                                <p class="text-sm text-slate-600 mt-1"><strong>Medications:</strong> {{ $record->medications }}</p>
                                            @endif
                                            @if($record->notes)
                                                <p class="text-sm text-slate-600 mt-1">{{ $record->notes }}</p>
                                            @endif
                                            <p class="text-xs text-slate-500 mt-2">Recorded: {{ $record->recorded_at->format('M d, Y') }}</p>
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            @if($record->document_path)
                                                <a href="{{ Storage::url($record->document_path) }}" target="_blank" class="text-sanctuary-gold hover:text-amber-700">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @can('update', $member)
                                                <form method="POST" action="{{ route('health-records.destroy', $record) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this health record?')" class="text-red-600 hover:text-red-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-slate-500 text-center py-8">{{ __('No health records yet.') }}</p>
                    @endif
                </x-slot>

                <x-slot name="skills">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Skills & Talents') }}</h3>
                        @can('update', $member)
                            <x-ui.primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-skill')">
                                {{ __('Add Skill') }}
                            </x-ui.primary-button>
                        @endcan
                    </div>
                    @if($member->skills->count() > 0)
                        @foreach(['pastoral' => 'Pastoral', 'practical' => 'Practical', 'special' => 'Special'] as $category => $label)
                            @php
                                $categorySkills = $member->skills->where('category', $category);
                            @endphp
                            @if($categorySkills->count() > 0)
                                <div class="mb-6">
                                    <h4 class="text-md font-medium text-slate-700 mb-3">{{ $label }} Skills</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($categorySkills as $skill)
                                            <div class="border border-stone-200 rounded-lg p-3">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <h5 class="font-medium text-slate-900">{{ $skill->name }}</h5>
                                                        <span class="text-xs text-slate-500 capitalize">{{ $skill->proficiency }}</span>
                                                        @if($skill->notes)
                                                            <p class="text-sm text-slate-600 mt-1">{{ $skill->notes }}</p>
                                                        @endif
                                                    </div>
                                                    @can('update', $member)
                                                        <form method="POST" action="{{ route('skills.destroy', $skill) }}" class="inline ml-2">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this skill?')" class="text-red-600 hover:text-red-800">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-slate-500 text-center py-8">{{ __('No skills recorded yet.') }}</p>
                    @endif
                </x-slot>

                <x-slot name="service">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Service History') }}</h3>
                        @can('update', $member)
                            <x-ui.primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-service-record')">
                                {{ __('Add Service Record') }}
                            </x-ui.primary-button>
                        @endcan
                    </div>
                    <x-features.service-history-list :assignments="$member->assignments" />
                </x-slot>

                <x-slot name="history">
                    <h3 class="text-lg font-medium text-slate-900 mb-4">{{ __('Audit Log') }}</h3>
                    <x-features.audit-trail :audits="$audits" />
                </x-slot>
            </x-features.related-records>
    </div>

    <!-- Add Formation Event Modal -->
    <x-ui.modal name="add-formation-event" focusable>
        <form method="post" action="{{ route('members.formation.store', $member) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add Formation Milestone') }}
            </h2>

            <div class="mt-6">
                <x-ui.label for="stage" value="{{ __('Stage') }}" />
                <x-ui.select id="stage" name="stage" class="mt-1">
                    @foreach(\App\Enums\FormationStage::cases() as $stage)
                        <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input-error :messages="$errors->get('stage')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="started_at" value="{{ __('Start Date') }}" />
                <x-ui.input id="started_at" name="started_at" type="date" class="mt-1 block w-full" required />
                <x-ui.input-error :messages="$errors->get('started_at')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="notes" value="{{ __('Notes (Optional)') }}" />
                <x-ui.textarea id="notes" name="notes" class="mt-1 block w-full" rows="3"></x-ui.textarea>
                <x-ui.input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-ui.secondary-button>

                <x-ui.primary-button class="ml-3">
                    {{ __('Save Milestone') }}
                </x-ui.primary-button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Upload Document Modals (one per event) -->
    @foreach($member->formationEvents as $event)
        <x-ui.modal name="upload-document-{{ $event->id }}" focusable>
            <form method="post" action="{{ route('formation.documents.store', $event) }}" enctype="multipart/form-data" class="p-6">
                @csrf
                <input type="hidden" name="formation_event_id" value="{{ $event->id }}">

                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Upload Document') }} - {{ $event->stage->label() }}
                </h2>

                <div class="mt-6">
                    <x-ui.label for="file-{{ $event->id }}" value="{{ __('Select File (PDF, JPG, PNG - Max 5MB)') }}" />
                    <input 
                        id="file-{{ $event->id }}" 
                        name="file" 
                        type="file" 
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-muted-gold file:text-white
                            hover:file:bg-amber-700"
                        required 
                    />
                    <x-ui.input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div class="mt-6">
                    <x-ui.label for="document_type-{{ $event->id }}" value="{{ __('Document Type (Optional)') }}" />
                    <x-ui.input 
                        id="document_type-{{ $event->id }}" 
                        name="document_type" 
                        type="text" 
                        class="mt-1 block w-full" 
                        placeholder="e.g., Baptismal Certificate, Health Report"
                    />
                    <x-ui.input-error :messages="$errors->get('document_type')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-ui.secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-ui.secondary-button>

                    <x-ui.primary-button class="ml-3">
                        {{ __('Upload Document') }}
                    </x-ui.primary-button>
                </div>
            </form>
        </x-ui.modal>

        <!-- View Documents Modal -->
        <x-ui.modal name="view-documents-{{ $event->id }}" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ __('Documents') }} - {{ $event->stage->label() }}
                </h2>

                @if($event->documents->count() > 0)
                    <div class="space-y-3">
                        @foreach($event->documents as $document)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $document->file_name }}</div>
                                    @if($document->document_type)
                                        <div class="text-sm text-gray-600">{{ $document->document_type }}</div>
                                    @endif
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ number_format($document->file_size / 1024, 2) }} KB
                                        • Uploaded {{ $document->created_at->format('M d, Y') }}
                                        by {{ $document->uploadedBy->name }}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 ml-4">
                                    @can('downloadDocument', $document)
                                        <a 
                                            href="{{ route('formation.documents.download', $document) }}" 
                                            class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                                        >
                                            Download
                                        </a>
                                    @endcan
                                    @can('deleteDocument', $document)
                                        <form method="post" action="{{ route('formation.documents.destroy', $document) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                                                onclick="return confirm('Are you sure you want to delete this document?')"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No documents uploaded yet.</p>
                @endif

                <div class="mt-6 flex justify-end">
                    <x-ui.secondary-button x-on:click="$dispatch('close')">
                        {{ __('Close') }}
                    </x-ui.secondary-button>
                </div>
            </div>
        </x-ui.modal>
    @endforeach

    <!-- Transfer Member Modal -->
    <x-ui.modal name="transfer-member" focusable>
        <form method="post" action="{{ route('members.transfer', $member) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Transfer Member') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Select the new community and the date of transfer. This will update the member\'s current location and add an entry to their service history.') }}
            </p>

            <div class="mt-6">
                <x-ui.label for="community_id" value="{{ __('New Community') }}" />
                <x-ui.select id="community_id" name="community_id" class="mt-1" required>
                    <option value="">{{ __('Select Community') }}</option>
                    @foreach($communities as $community)
                        @if($community->id !== $member->community_id)
                            <option value="{{ $community->id }}">{{ $community->name }}</option>
                        @endif
                    @endforeach
                </x-ui.select>
                <x-ui.input-error :messages="$errors->get('community_id')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="transfer_date" value="{{ __('Transfer Date') }}" />
                <x-ui.input id="transfer_date" name="transfer_date" type="date" class="mt-1 block w-full" :value="now()->format('Y-m-d')" required />
                <x-ui.input-error :messages="$errors->get('transfer_date')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-ui.secondary-button>

                <x-ui.primary-button class="ml-3">
                    {{ __('Confirm Transfer') }}
                </x-ui.primary-button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Update Photo Modal -->
    <x-ui.modal name="update-photo" focusable>
        <form method="post" action="{{ route('members.photo.update', $member) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Update Profile Photo') }}
            </h2>

            <div class="mt-6">
                <x-ui.label for="photo" value="{{ __('Select Photo (JPG, PNG - Max 2MB)') }}" />
                <input 
                    id="photo" 
                    name="photo" 
                    type="file" 
                    accept=".jpg,.jpeg,.png"
                    class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100"
                    required 
                />
                <x-ui.input-error :messages="$errors->get('photo')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-ui.secondary-button>

                <x-ui.primary-button class="ml-3">
                    {{ __('Save Photo') }}
                </x-ui.primary-button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Add Health Record Modal -->
    <x-ui.modal name="add-health-record" focusable>
        <form method="post" action="{{ route('members.health.store', $member) }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add Health Record') }}
            </h2>

            <div class="mt-6">
                <x-ui.label for="condition" value="{{ __('Condition') }}" />
                <x-ui.input id="condition" name="condition" type="text" class="mt-1 block w-full" required />
                <x-ui.input-error :messages="$errors->get('condition')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="medications" value="{{ __('Medications (Optional)') }}" />
                <x-ui.textarea id="medications" name="medications" class="mt-1 block w-full" rows="2"></x-ui.textarea>
                <x-ui.input-error :messages="$errors->get('medications')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="health_notes" value="{{ __('Notes (Optional)') }}" />
                <x-ui.textarea id="health_notes" name="notes" class="mt-1 block w-full" rows="3"></x-ui.textarea>
                <x-ui.input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="recorded_at" value="{{ __('Recorded Date') }}" />
                <x-ui.input id="recorded_at" name="recorded_at" type="date" class="mt-1 block w-full" :value="now()->format('Y-m-d')" required />
                <x-ui.input-error :messages="$errors->get('recorded_at')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="health_document" value="{{ __('Document (Optional - PDF, JPG, PNG - Max 5MB)') }}" />
                <input 
                    id="health_document" 
                    name="document" 
                    type="file" 
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-sanctuary-green/10 file:text-sanctuary-green
                        hover:file:bg-sanctuary-green/20"
                />
                <x-ui.input-error :messages="$errors->get('document')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-ui.secondary-button>

                <x-ui.primary-button class="ml-3">
                    {{ __('Save Health Record') }}
                </x-ui.primary-button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Add Skill Modal -->
    <x-ui.modal name="add-skill" focusable>
        <form method="post" action="{{ route('members.skills.store', $member) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add Skill') }}
            </h2>

            <div class="mt-6">
                <x-ui.label for="skill_category" value="{{ __('Category') }}" />
                <x-ui.select id="skill_category" name="category" class="mt-1" required>
                    <option value="">{{ __('Select Category') }}</option>
                    <option value="pastoral">{{ __('Pastoral') }}</option>
                    <option value="practical">{{ __('Practical') }}</option>
                    <option value="special">{{ __('Special') }}</option>
                </x-ui.select>
                <x-ui.input-error :messages="$errors->get('category')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="skill_name" value="{{ __('Skill Name') }}" />
                <x-ui.input id="skill_name" name="name" type="text" class="mt-1 block w-full" placeholder="e.g., Teaching, Cooking, Music" required />
                <x-ui.input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="proficiency" value="{{ __('Proficiency Level') }}" />
                <x-ui.select id="proficiency" name="proficiency" class="mt-1" required>
                    <option value="beginner">{{ __('Beginner') }}</option>
                    <option value="intermediate" selected>{{ __('Intermediate') }}</option>
                    <option value="advanced">{{ __('Advanced') }}</option>
                    <option value="expert">{{ __('Expert') }}</option>
                </x-ui.select>
                <x-ui.input-error :messages="$errors->get('proficiency')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="skill_notes" value="{{ __('Notes (Optional)') }}" />
                <x-ui.textarea id="skill_notes" name="notes" class="mt-1 block w-full" rows="2" placeholder="Additional details about this skill"></x-ui.textarea>
                <x-ui.input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-ui.secondary-button>

                <x-ui.primary-button class="ml-3">
                    {{ __('Save Skill') }}
                </x-ui.primary-button>
            </div>
        </form>
    </x-ui.modal>
    <!-- Add Service Record Modal -->
    <x-ui.modal name="add-service-record" focusable>
        <form method="post" action="{{ route('members.assignments.store', $member) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add Service Record') }}
            </h2>

            <div class="mt-6">
                <x-ui.label for="assignment_community_id" value="{{ __('Community') }}" />
                <x-ui.select id="assignment_community_id" name="community_id" class="mt-1" required>
                    <option value="">{{ __('Select Community') }}</option>
                    @foreach($communities as $community)
                        <option value="{{ $community->id }}">{{ $community->name }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input-error :messages="$errors->get('community_id')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-ui.label for="role" value="{{ __('Role (Optional)') }}" />
                <x-ui.input id="role" name="role" type="text" class="mt-1 block w-full" placeholder="e.g., Director, Assistant" />
                <x-ui.input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <div>
                    <x-ui.label for="start_date" value="{{ __('Start Date') }}" />
                    <x-ui.input id="start_date" name="start_date" type="date" class="mt-1 block w-full" required />
                    <x-ui.input-error :messages="$errors->get('start_date')" class="mt-2" />
                </div>
                <div>
                    <x-ui.label for="end_date" value="{{ __('End Date (Optional)') }}" />
                    <x-ui.input id="end_date" name="end_date" type="date" class="mt-1 block w-full" />
                    <x-ui.input-error :messages="$errors->get('end_date')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-ui.secondary-button>

                <x-ui.primary-button class="ml-3">
                    {{ __('Save Record') }}
                </x-ui.primary-button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Update Photo Modal -->
    <x-ui.modal name="update-photo" focusable>
        <form method="post" action="{{ route('members.photo.update', $member) }}" class="p-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Update Profile Photo') }}
            </h2>

            <div class="mt-6">
                <x-ui.label for="photo" value="{{ __('Photo') }}" />
                <input id="photo" name="photo" type="file" class="mt-1 block w-full text-sm text-slate-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100" accept="image/*" required />
                <x-ui.input-error :messages="$errors->get('photo')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-ui.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-ui.secondary-button>

                <x-ui.primary-button class="ml-3">
                    {{ __('Save Photo') }}
                </x-ui.primary-button>
            </div>
        </form>
    </x-ui.modal>
</x-app-layout>
