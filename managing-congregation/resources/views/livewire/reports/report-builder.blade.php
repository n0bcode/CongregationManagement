<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-stone-800">
                    {{ __('Report Builder') }}
                </h2>
                <p class="mt-2 text-base text-slate-600">
                    {{ __('Create custom reports by selecting data sources, fields, and filters.') }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <x-button variant="secondary" wire:click="$set('showSaveModal', true)" class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    {{ __('Save Template') }}
                </x-button>
                
                <div x-data="{ open: false }" class="relative">
                    <x-button variant="primary" @click="open = !open" class="flex items-center justify-center w-full sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>{{ __('Export') }}</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </x-button>
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false" 
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50 border border-stone-200 overflow-hidden" 
                         style="display: none;">
                        <div class="py-1">
                            <button wire:click="export('csv')" @click="open = false" class="flex items-center w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <div class="font-medium">{{ __('CSV Format') }}</div>
                                    <div class="text-xs text-slate-500">{{ __('Excel compatible') }}</div>
                                </div>
                            </button>
                            <button wire:click="export('excel')" @click="open = false" class="flex items-center w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <div class="font-medium">{{ __('Excel Format') }}</div>
                                    <div class="text-xs text-slate-500">{{ __('With formatting') }}</div>
                                </div>
                            </button>
                            <button wire:click="export('pdf')" @click="open = false" class="flex items-center w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <div class="font-medium">{{ __('PDF Format') }}</div>
                                    <div class="text-xs text-slate-500">{{ __('Print ready') }}</div>
                                </div>
                            </button>
                            <button wire:click="export('json')" @click="open = false" class="flex items-center w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 transition-colors border-t border-stone-100">
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                <div>
                                    <div class="font-medium">{{ __('JSON Format') }}</div>
                                    <div class="text-xs text-slate-500">{{ __('For developers') }}</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Configuration Panel -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden">
                    <!-- Panel Header -->
                    <div class="bg-gradient-to-r from-amber-50 to-stone-50 px-6 py-4 border-b border-stone-200">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-amber-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <h3 class="text-xl font-semibold text-stone-800">{{ __('Configuration') }}</h3>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Source Selection -->
                        <div>
                            <label class="block text-base font-medium text-slate-700 mb-2">
                                {{ __('Data Source') }} <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model.live="source" class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none appearance-none pr-10">
                                    @foreach($availableSources as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ __('Select the type of data you want to report on') }}</p>
                        </div>

                        <!-- Fields Selection -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-base font-medium text-slate-700">
                                    {{ __('Fields to Include') }} <span class="text-rose-600">*</span>
                                </label>
                                <span class="text-xs text-slate-500 bg-stone-100 px-2 py-1 rounded-full">
                                    {{ count($selectedFields) }}/{{ count($availableFields) }}
                                </span>
                            </div>
                            <div class="space-y-1 max-h-64 overflow-y-auto border border-stone-200 rounded-lg p-3 bg-stone-50">
                                @foreach($availableFields as $key => $label)
                                    <label class="flex items-center space-x-3 p-2 rounded-md hover:bg-white cursor-pointer transition-colors group">
                                        <input type="checkbox" 
                                               wire:model.live="selectedFields" 
                                               value="{{ $key }}" 
                                               class="rounded border-stone-300 text-amber-600 focus:ring-amber-500 focus:ring-offset-0 w-5 h-5 cursor-pointer">
                                        <span class="text-sm text-slate-700 group-hover:text-slate-900 flex-1">{{ $label }}</span>
                                        <svg class="w-4 h-4 text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ __('Select which columns to include in your report') }}</p>
                        </div>

                        <!-- Filters -->
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <label class="text-base font-medium text-slate-700">
                                    {{ __('Filters') }}
                                </label>
                                <button wire:click="addFilter('new_key', '')" 
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-md transition-colors border border-amber-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('Add Filter') }}
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($filters as $key => $value)
                                    <div class="bg-white p-4 rounded-lg border border-stone-200 shadow-sm">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-1 space-y-3">
                                                <input type="text" 
                                                       wire:model.live="filters.{{ $key }}.key" 
                                                       placeholder="{{ __('Field name') }}" 
                                                       class="w-full min-h-[44px] px-3 py-2 text-sm text-slate-800 bg-white border border-stone-300 rounded-md focus:border-amber-600 focus:ring-2 focus:ring-amber-500 focus:outline-none placeholder:text-slate-400">
                                                <input type="text" 
                                                       wire:model.live="filters.{{ $key }}.value" 
                                                       placeholder="{{ __('Filter value') }}" 
                                                       class="w-full min-h-[44px] px-3 py-2 text-sm text-slate-800 bg-white border border-stone-300 rounded-md focus:border-amber-600 focus:ring-2 focus:ring-amber-500 focus:outline-none placeholder:text-slate-400">
                                            </div>
                                            <button wire:click="removeFilter('{{ $key }}')" 
                                                    class="flex-shrink-0 p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors"
                                                    title="{{ __('Remove filter') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if(empty($filters))
                                    <div class="text-center py-8 px-4 bg-stone-50 rounded-lg border-2 border-dashed border-stone-200">
                                        <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                        <p class="text-sm text-slate-500 font-medium">{{ __('No filters applied') }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('Click "Add Filter" to refine your results') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Panel -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 h-full flex flex-col overflow-hidden">
                    <!-- Preview Header -->
                    <div class="bg-gradient-to-r from-stone-50 to-slate-50 px-6 py-4 border-b border-stone-200">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-slate-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <h3 class="text-xl font-semibold text-stone-800">{{ __('Data Preview') }}</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center text-sm text-slate-600 bg-white px-3 py-1.5 rounded-full border border-stone-200 shadow-sm">
                                    <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Showing first 10 results') }}
                                </span>
                                @if(count($previewData) > 0)
                                    <span class="inline-flex items-center text-sm font-medium text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ count($previewData) }} {{ __('rows') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table Container -->
                    <div class="flex-1 overflow-auto">
                        @if(count($selectedFields) > 0)
                            <table class="min-w-full divide-y divide-stone-200">
                                <thead class="bg-stone-50 sticky top-0 z-10 shadow-sm">
                                    <tr>
                                        @foreach($selectedFields as $field)
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider whitespace-nowrap border-b-2 border-stone-200">
                                                <div class="flex items-center space-x-2">
                                                    <span>{{ $availableFields[$field] ?? $field }}</span>
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-stone-100">
                                    @forelse($previewData as $index => $row)
                                        <tr class="hover:bg-amber-50 transition-colors duration-150 group">
                                            @foreach($selectedFields as $field)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 group-hover:text-slate-900">
                                                    <div class="flex items-center">
                                                        @if($loop->first)
                                                            <span class="inline-flex items-center justify-center w-6 h-6 mr-3 text-xs font-medium text-slate-500 bg-stone-100 rounded-full group-hover:bg-amber-100 group-hover:text-amber-700 transition-colors">
                                                                {{ $index + 1 }}
                                                            </span>
                                                        @endif
                                                        <span class="truncate max-w-xs" title="{{ data_get($row, $field) }}">
                                                            {{ data_get($row, $field) ?: '—' }}
                                                        </span>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($selectedFields) }}" class="px-6 py-16 text-center">
                                                <x-empty-state 
                                                    icon="chart-bar"
                                                    title="{{ __('No data found') }}"
                                                    description="{{ __('No records match your current filters. Try adjusting your criteria or selecting a different data source.') }}"
                                                />
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @else
                            <div class="flex items-center justify-center h-full p-12">
                                <div class="text-center max-w-md">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-stone-100 rounded-full mb-6">
                                        <svg class="w-10 h-10 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-slate-800 mb-2">{{ __('Select Fields to Preview') }}</h3>
                                    <p class="text-base text-slate-600 mb-6">{{ __('Choose at least one field from the configuration panel to see your report preview.') }}</p>
                                    <div class="inline-flex items-center text-sm text-amber-700 bg-amber-50 px-4 py-2 rounded-lg border border-amber-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                        {{ __('Start by selecting fields on the left') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer Stats -->
                    @if(count($previewData) > 0)
                        <div class="bg-stone-50 px-6 py-3 border-t border-stone-200">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-6 text-slate-600">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <strong class="font-medium">{{ count($selectedFields) }}</strong>&nbsp;{{ __('columns') }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <strong class="font-medium">{{ count($previewData) }}</strong>&nbsp;{{ __('rows') }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                        <strong class="font-medium">{{ count($filters) }}</strong>&nbsp;{{ __('filters') }}
                                    </span>
                                </div>
                                <span class="text-xs text-slate-500">
                                    {{ __('Last updated:') }} {{ now()->format('H:i:s') }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Save Template Modal -->
        @if($showSaveModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
                 x-data="{ show: @entangle('showSaveModal') }"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Backdrop -->
                    <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" 
                         aria-hidden="true" 
                         wire:click="$set('showSaveModal', false)"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal Panel -->
                    <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-amber-50 to-stone-50 px-6 py-5 border-b border-stone-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 border-2 border-amber-200">
                                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-xl font-semibold text-stone-800" id="modal-title">
                                        {{ __('Save Report Template') }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ __('Save your current configuration for future use') }}
                                    </p>
                                </div>
                                <button wire:click="$set('showSaveModal', false)" 
                                        class="flex-shrink-0 ml-4 text-slate-400 hover:text-slate-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="bg-white px-6 py-6">
                            <div class="space-y-6">
                                <!-- Template Name -->
                                <div>
                                    <label class="block text-base font-medium text-slate-700 mb-2">
                                        {{ __('Template Name') }} <span class="text-rose-600">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="templateName" 
                                           class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none placeholder:text-slate-400" 
                                           placeholder="{{ __('e.g., Monthly Member Report') }}"
                                           autofocus>
                                    @error('templateName') 
                                        <p class="mt-2 text-sm text-rose-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Template Summary -->
                                <div class="bg-stone-50 rounded-lg p-4 border border-stone-200">
                                    <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        {{ __('Template Configuration') }}
                                    </h4>
                                    <dl class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <dt class="text-slate-600">{{ __('Data Source:') }}</dt>
                                            <dd class="font-medium text-slate-800">{{ $availableSources[$source] ?? $source }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-slate-600">{{ __('Fields:') }}</dt>
                                            <dd class="font-medium text-slate-800">{{ count($selectedFields) }} {{ __('selected') }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-slate-600">{{ __('Filters:') }}</dt>
                                            <dd class="font-medium text-slate-800">{{ count($filters) }} {{ __('applied') }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-stone-50 px-6 py-4 border-t border-stone-200 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <x-button variant="secondary" 
                                      wire:click="$set('showSaveModal', false)" 
                                      class="w-full sm:w-auto justify-center">
                                {{ __('Cancel') }}
                            </x-button>
                            <x-button variant="primary" 
                                      wire:click="saveTemplate" 
                                      class="w-full sm:w-auto justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Save Template') }}
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
