<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Document Details') }}">
            <x-slot:actions>
                <x-ui.button variant="secondary" href="{{ route('documents.index') }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Back to Documents') }}
                </x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Document Header --}}
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-t-lg shadow-sm p-8 text-white">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-3xl font-bold mb-2">{{ $document->title }}</h3>
                        <div class="flex items-center gap-4 text-amber-100">
                            <span class="inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                {{ ucfirst(str_replace('_', ' ', $document->category->value)) }}
                            </span>
                            <span class="inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                {{ $document->folder?->name ?? __('Root') }}
                            </span>
                            <span class="inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                                {{ $document->file_size_human }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        @if(str_contains($document->mime_type, 'pdf'))
                            <div class="h-16 w-16 flex items-center justify-center rounded-lg bg-white/20">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @elseif(str_contains($document->mime_type, 'image'))
                            <div class="h-16 w-16 flex items-center justify-center rounded-lg bg-white/20">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @else
                            <div class="h-16 w-16 flex items-center justify-center rounded-lg bg-white/20">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Document Details --}}
            <div class="bg-white rounded-b-lg shadow-sm border border-stone-200 p-8">
                {{-- Description --}}
                @if($document->description)
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-stone-800 mb-2">{{ __('Description') }}</h4>
                        <p class="text-base text-slate-700 leading-relaxed">{{ $document->description }}</p>
                    </div>
                @endif

                {{-- Document Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h4 class="text-sm font-semibold text-stone-600 uppercase tracking-wider mb-2">{{ __('File Name') }}</h4>
                        <p class="text-base text-slate-800">{{ $document->file_name }}</p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-stone-600 uppercase tracking-wider mb-2">{{ __('File Type') }}</h4>
                        <p class="text-base text-slate-800">{{ strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION)) }}</p>
                    </div>

                    @if($document->community)
                        <div>
                            <h4 class="text-sm font-semibold text-stone-600 uppercase tracking-wider mb-2">{{ __('Community') }}</h4>
                            <p class="text-base text-slate-800">{{ $document->community->name }}</p>
                        </div>
                    @endif

                    @if($document->member)
                        <div>
                            <h4 class="text-sm font-semibold text-stone-600 uppercase tracking-wider mb-2">{{ __('Related Member') }}</h4>
                            <p class="text-base text-slate-800">
                                {{ $document->member->first_name }} {{ $document->member->last_name }}
                                @if($document->member->religious_name)
                                    ({{ $document->member->religious_name }})
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Metadata --}}
                <div class="border-t border-stone-200 pt-6">
                    <h4 class="text-lg font-semibold text-stone-800 mb-4">{{ __('Metadata') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center text-slate-600">
                            <svg class="w-5 h-5 mr-2 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>{{ __('Uploaded by') }}: <strong class="text-slate-800">{{ $document->uploader->name }}</strong></span>
                        </div>
                        <div class="flex items-center text-slate-600">
                            <svg class="w-5 h-5 mr-2 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ __('Uploaded on') }}: <strong class="text-slate-800">{{ $document->created_at->format('F d, Y \a\t g:i A') }}</strong></span>
                        </div>
                        @if($document->updated_at->ne($document->created_at))
                            <div class="flex items-center text-slate-600">
                                <svg class="w-5 h-5 mr-2 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>{{ __('Last updated') }}: <strong class="text-slate-800">{{ $document->updated_at->format('F d, Y \a\t g:i A') }}</strong></span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-4 pt-8 border-t border-stone-200 mt-8">
                    <x-ui.button variant="primary" href="{{ $document->getDownloadUrl() }}" class="flex-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('Download') }}
                    </x-ui.button>

                    @can('update', $document)
                        <x-ui.button variant="secondary" href="{{ route('documents.edit', $document) }}" class="flex-1">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ __('Edit') }}
                        </x-ui.button>
                    @endcan

                    @can('delete', $document)
                        <form method="POST" action="{{ route('documents.destroy', $document) }}" class="flex-1" onsubmit="return confirm('{{ __('Are you sure you want to delete this document? This action cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="danger" class="w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                {{ __('Delete') }}
                            </x-ui.button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
