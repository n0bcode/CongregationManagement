<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Member Profile') }}
            </h2>
            <div class="flex items-center space-x-4">
                @foreach($actions as $action)
                    @if($action->method === 'GET')
                        <a href="{{ $action->url }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 {{ $action->variant === 'danger' ? 'text-red-600 hover:text-red-700 hover:bg-red-50' : '' }}">
                            @if($action->icon)
                                <!-- Icon rendering could be improved with a component -->
                                <span class="mr-2">{{ $action->label }}</span>
                            @else
                                {{ $action->label }}
                            @endif
                        </a>
                    @else
                        <form method="POST" action="{{ $action->url }}" class="inline">
                            @csrf
                            @method($action->method)
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 {{ $action->variant === 'danger' ? 'text-red-600 hover:text-red-700 hover:bg-red-50' : '' }}"
                                @if($action->confirm) onclick="return confirm('{{ $action->confirmMessage }}')" @endif
                            >
                                {{ $action->label }}
                            </button>
                        </form>
                    @endif
                @endforeach
                
                @can('update', $member)
                    <x-secondary-button
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'transfer-member')"
                        class="ml-2"
                    >
                        {{ __('Transfer') }}
                    </x-secondary-button>
                @endcan

                <a href="{{ route('members.index') }}" class="text-sm text-gray-600 hover:text-gray-900 ml-4">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'update-photo')">
                                    {{ __('Update Photo') }}
                                </x-secondary-button>
                                @if($member->profile_photo_path)
                                    <form method="POST" action="{{ route('members.photo.destroy', $member) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button onclick="return confirm('Are you sure you want to remove this photo?')" class="w-full">
                                            {{ __('Remove Photo') }}
                                        </x-danger-button>
                                    </form>
                                @endif
                            </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Related Records Tabs -->
            <x-related-records :tabs="[
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
                            </dl>
                        </div>
                    </div>
                </x-slot>

                <x-slot name="formation">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Formation Timeline') }}</h3>
                        @can('create', \App\Models\FormationEvent::class)
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-formation-event')">
                                {{ __('Add Milestone') }}
                            </x-primary-button>
                        @endcan
                    </div>
                    <x-formation-timeline :events="$member->formationEvents" :projectedEvents="$projectedEvents" />
                </x-slot>

                <x-slot name="health">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Health Records') }}</h3>
                        @can('update', $member)
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-health-record')">
                                {{ __('Add Health Record') }}
                            </x-primary-button>
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
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-skill')">
                                {{ __('Add Skill') }}
                            </x-primary-button>
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
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-service-record')">
                                {{ __('Add Service Record') }}
                            </x-primary-button>
                        @endcan
                    </div>
                    <x-service-history-list :assignments="$member->assignments" />
                </x-slot>

                <x-slot name="history">
                    <h3 class="text-lg font-medium text-slate-900 mb-4">{{ __('Audit Log') }}</h3>
                    <x-audit-trail :audits="$member->audits" />
                </x-slot>
            </x-related-records>
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

    <!-- Upload Document Modals (one per event) -->
    @foreach($member->formationEvents as $event)
        <x-modal name="upload-document-{{ $event->id }}" focusable>
            <form method="post" action="{{ route('formation.documents.store', $event) }}" enctype="multipart/form-data" class="p-6">
                @csrf
                <input type="hidden" name="formation_event_id" value="{{ $event->id }}">

                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Upload Document') }} - {{ $event->stage->label() }}
                </h2>

                <div class="mt-6">
                    <x-input-label for="file-{{ $event->id }}" value="{{ __('Select File (PDF, JPG, PNG - Max 5MB)') }}" />
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
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div class="mt-6">
                    <x-input-label for="document_type-{{ $event->id }}" value="{{ __('Document Type (Optional)') }}" />
                    <x-text-input 
                        id="document_type-{{ $event->id }}" 
                        name="document_type" 
                        type="text" 
                        class="mt-1 block w-full" 
                        placeholder="e.g., Baptismal Certificate, Health Report"
                    />
                    <x-input-error :messages="$errors->get('document_type')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-3">
                        {{ __('Upload Document') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- View Documents Modal -->
        <x-modal name="view-documents-{{ $event->id }}" focusable>
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
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Close') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>
    @endforeach

    <!-- Transfer Member Modal -->
    <x-modal name="transfer-member" focusable>
        <form method="post" action="{{ route('members.transfer', $member) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Transfer Member') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Select the new community and the date of transfer. This will update the member\'s current location and add an entry to their service history.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="community_id" value="{{ __('New Community') }}" />
                <select id="community_id" name="community_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">{{ __('Select Community') }}</option>
                    @foreach($communities as $community)
                        @if($community->id !== $member->community_id)
                            <option value="{{ $community->id }}">{{ $community->name }}</option>
                        @endif
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('community_id')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="transfer_date" value="{{ __('Transfer Date') }}" />
                <x-text-input id="transfer_date" name="transfer_date" type="date" class="mt-1 block w-full" :value="now()->format('Y-m-d')" required />
                <x-input-error :messages="$errors->get('transfer_date')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Confirm Transfer') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Update Photo Modal -->
    <x-modal name="update-photo" focusable>
        <form method="post" action="{{ route('members.photo.update', $member) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Update Profile Photo') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="photo" value="{{ __('Select Photo (JPG, PNG - Max 2MB)') }}" />
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
                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Save Photo') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Add Health Record Modal -->
    <x-modal name="add-health-record" focusable>
        <form method="post" action="{{ route('members.health.store', $member) }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add Health Record') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="condition" value="{{ __('Condition') }}" />
                <x-text-input id="condition" name="condition" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('condition')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="medications" value="{{ __('Medications (Optional)') }}" />
                <textarea id="medications" name="medications" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2"></textarea>
                <x-input-error :messages="$errors->get('medications')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="health_notes" value="{{ __('Notes (Optional)') }}" />
                <textarea id="health_notes" name="notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="recorded_at" value="{{ __('Recorded Date') }}" />
                <x-text-input id="recorded_at" name="recorded_at" type="date" class="mt-1 block w-full" :value="now()->format('Y-m-d')" required />
                <x-input-error :messages="$errors->get('recorded_at')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="health_document" value="{{ __('Document (Optional - PDF, JPG, PNG - Max 5MB)') }}" />
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
                <x-input-error :messages="$errors->get('document')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Save Health Record') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Add Skill Modal -->
    <x-modal name="add-skill" focusable>
        <form method="post" action="{{ route('members.skills.store', $member) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add Skill') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="skill_category" value="{{ __('Category') }}" />
                <select id="skill_category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">{{ __('Select Category') }}</option>
                    <option value="pastoral">{{ __('Pastoral') }}</option>
                    <option value="practical">{{ __('Practical') }}</option>
                    <option value="special">{{ __('Special') }}</option>
                </select>
                <x-input-error :messages="$errors->get('category')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="skill_name" value="{{ __('Skill Name') }}" />
                <x-text-input id="skill_name" name="name" type="text" class="mt-1 block w-full" placeholder="e.g., Teaching, Cooking, Music" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="proficiency" value="{{ __('Proficiency Level') }}" />
                <select id="proficiency" name="proficiency" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="beginner">{{ __('Beginner') }}</option>
                    <option value="intermediate" selected>{{ __('Intermediate') }}</option>
                    <option value="advanced">{{ __('Advanced') }}</option>
                    <option value="expert">{{ __('Expert') }}</option>
                </select>
                <x-input-error :messages="$errors->get('proficiency')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="skill_notes" value="{{ __('Notes (Optional)') }}" />
                <textarea id="skill_notes" name="notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2" placeholder="Additional details about this skill"></textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Save Skill') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    <!-- Add Service Record Modal -->
    <x-modal name="add-service-record" focusable>
        <form method="post" action="{{ route('members.assignments.store', $member) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add Service Record') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="assignment_community_id" value="{{ __('Community') }}" />
                <select id="assignment_community_id" name="community_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">{{ __('Select Community') }}</option>
                    @foreach($communities as $community)
                        <option value="{{ $community->id }}">{{ $community->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('community_id')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="role" value="{{ __('Role (Optional)') }}" />
                <x-text-input id="role" name="role" type="text" class="mt-1 block w-full" placeholder="e.g., Director, Assistant" />
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="start_date" value="{{ __('Start Date') }}" />
                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="end_date" value="{{ __('End Date (Optional)') }}" />
                    <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Save Record') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
