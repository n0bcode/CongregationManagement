<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Edit Document') }}" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
                <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-lg font-medium text-slate-700 mb-2">
                            {{ __('Title') }} <span class="text-rose-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $document->title) }}"
                            required
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('title') border-rose-500 @enderror"
                            placeholder="{{ __('Enter document title...') }}"
                        >
                        @error('title')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-lg font-medium text-slate-700 mb-2">
                            {{ __('Description') }}
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('description') border-rose-500 @enderror"
                            placeholder="{{ __('Enter document description...') }}"
                        >{{ old('description', $document->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category and Folder --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Category --}}
                        <div>
                            <label for="category" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Category') }} <span class="text-rose-600">*</span>
                            </label>
                            <select
                                id="category"
                                name="category"
                                required
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('category') border-rose-500 @enderror"
                            >
                                <option value="">{{ __('Select category...') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->value }}" {{ old('category', $document->category->value) === $category->value ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $category->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Folder --}}
                        <div>
                            <label for="folder_id" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Folder') }}
                            </label>
                            <select
                                id="folder_id"
                                name="folder_id"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('folder_id') border-rose-500 @enderror"
                            >
                                <option value="">{{ __('No folder (root)') }}</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ old('folder_id', $document->folder_id) == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                    @foreach($folder->children as $child)
                                        <option value="{{ $child->id }}" {{ old('folder_id', $document->folder_id) == $child->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;└─ {{ $child->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('folder_id')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Community and Member --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Community --}}
                        <div>
                            <label for="community_id" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Community') }}
                            </label>
                            <select
                                id="community_id"
                                name="community_id"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('community_id') border-rose-500 @enderror"
                            >
                                <option value="">{{ __('Not specific to a community') }}</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ old('community_id', $document->community_id) == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('community_id')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Member --}}
                        <div>
                            <label for="member_id" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Related Member') }}
                            </label>
                            <select
                                id="member_id"
                                name="member_id"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('member_id') border-rose-500 @enderror"
                            >
                                <option value="">{{ __('Not specific to a member') }}</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id', $document->member_id) == $member->id ? 'selected' : '' }}>
                                        {{ $member->first_name }} {{ $member->last_name }}
                                        @if($member->religious_name)
                                            ({{ $member->religious_name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Current File Info --}}
                    <div class="bg-stone-50 border border-stone-200 rounded-lg p-4">
                        <h4 class="text-base font-semibold text-stone-800 mb-2">{{ __('Current File') }}</h4>
                        <div class="flex items-center text-sm text-slate-700">
                            <svg class="w-5 h-5 mr-2 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>{{ $document->file_name }} ({{ $document->file_size_human }})</span>
                        </div>
                    </div>

                    {{-- File Upload (Optional) --}}
                    <div>
                        <label for="file" class="block text-lg font-medium text-slate-700 mb-2">
                            {{ __('Replace File') }} <span class="text-slate-500">({{ __('Optional') }})</span>
                        </label>
                        <input
                            type="file"
                            id="file"
                            name="file"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('file') border-rose-500 @enderror"
                        >
                        <p class="mt-2 text-sm text-slate-600">
                            {{ __('Leave empty to keep the current file. Accepted formats: PDF, DOC, DOCX, JPG, PNG. Maximum size: 10MB') }}
                        </p>
                        @error('file')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex gap-4 pt-6 border-t border-stone-200">
                        <x-ui.button type="submit" variant="primary" class="flex-1">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('Update Document') }}
                        </x-ui.button>
                        <x-ui.button variant="secondary" href="{{ route('documents.show', $document) }}" class="flex-1">
                            {{ __('Cancel') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
