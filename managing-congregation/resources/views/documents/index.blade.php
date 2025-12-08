<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Documents') }}
            </h2>
            @can('create', App\Models\Document::class)
                <x-ui.button variant="primary" href="{{ route('documents.create') }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Upload Document') }}
                </x-ui.button>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Search and Filters --}}
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6 mb-6">
                <form method="GET" action="{{ route('documents.index') }}" class="space-y-6">
                    {{-- Search --}}
                    <div>
                        <label for="search" class="block text-lg font-medium text-slate-700 mb-2">
                            {{ __('Search') }}
                        </label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="{{ __('Search by title, description, or filename...') }}"
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
                        >
                    </div>

                    {{-- Filters Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Category Filter --}}
                        <div>
                            <label for="category" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Category') }}
                            </label>
                            <select
                                id="category"
                                name="category"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
                            >
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->value }}" {{ request('category') === $category->value ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $category->value)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Community Filter --}}
                        <div>
                            <label for="community_id" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Community') }}
                            </label>
                            <select
                                id="community_id"
                                name="community_id"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
                            >
                                <option value="">{{ __('All Communities') }}</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ request('community_id') == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Folder Filter --}}
                        <div>
                            <label for="folder_id" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Folder') }}
                            </label>
                            <select
                                id="folder_id"
                                name="folder_id"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
                            >
                                <option value="">{{ __('All Folders') }}</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ request('folder_id') == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                    @foreach($folder->children as $child)
                                        <option value="{{ $child->id }}" {{ request('folder_id') == $child->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;└─ {{ $child->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('From Date') }}
                            </label>
                            <input
                                type="date"
                                id="start_date"
                                name="start_date"
                                value="{{ request('start_date') }}"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
                            >
                        </div>
                        <div>
                            <label for="end_date" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('To Date') }}
                            </label>
                            <input
                                type="date"
                                id="end_date"
                                name="end_date"
                                value="{{ request('end_date') }}"
                                class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
                            >
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-4 pt-4 border-t border-stone-200">
                        <x-ui.button type="submit" variant="primary" class="flex-1">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            {{ __('Search') }}
                        </x-ui.button>
                        <x-ui.button variant="secondary" href="{{ route('documents.index') }}" class="flex-1">
                            {{ __('Clear Filters') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>

            {{-- Documents List --}}
            @if($documents->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200">
                            <thead class="bg-stone-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-stone-700 uppercase tracking-wider">
                                        {{ __('Title') }}
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-stone-700 uppercase tracking-wider">
                                        {{ __('Category') }}
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-stone-700 uppercase tracking-wider">
                                        {{ __('Folder') }}
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-stone-700 uppercase tracking-wider">
                                        {{ __('Size') }}
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-stone-700 uppercase tracking-wider">
                                        {{ __('Uploaded') }}
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-sm font-semibold text-stone-700 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-stone-200">
                                @foreach($documents as $document)
                                    <tr class="hover:bg-stone-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                {{-- File Icon --}}
                                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                                    @if(str_contains($document->mime_type, 'pdf'))
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    @elseif(str_contains($document->mime_type, 'image'))
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-base font-medium text-slate-800">
                                                        {{ $document->title }}
                                                    </div>
                                                    @if($document->description)
                                                        <div class="text-sm text-slate-500">
                                                            {{ Str::limit($document->description, 50) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst(str_replace('_', ' ', $document->category->value)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-base text-slate-700">
                                            {{ $document->folder?->name ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-base text-slate-700">
                                            {{ $document->file_size_human }}
                                        </td>
                                        <td class="px-6 py-4 text-base text-slate-700">
                                            <div>{{ $document->created_at->format('M d, Y') }}</div>
                                            <div class="text-sm text-slate-500">{{ $document->uploader->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <x-ui.button variant="ghost" size="sm" href="{{ route('documents.show', $document) }}">
                                                    {{ __('View') }}
                                                </x-ui.button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="px-6 py-4 border-t border-stone-200">
                        {{ $documents->links() }}
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-4 text-xl font-semibold text-stone-800">{{ __('No documents found') }}</h3>
                    <p class="mt-2 text-base text-stone-600">
                        @if(request()->hasAny(['search', 'category', 'community_id', 'folder_id', 'start_date', 'end_date']))
                            {{ __('Try adjusting your filters or search terms.') }}
                        @else
                            {{ __('Get started by uploading your first document.') }}
                        @endif
                    </p>
                    @can('create', App\Models\Document::class)
                        <div class="mt-6">
                            <x-ui.button variant="primary" href="{{ route('documents.create') }}">
                                {{ __('Upload Document') }}
                            </x-ui.button>
                        </div>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
