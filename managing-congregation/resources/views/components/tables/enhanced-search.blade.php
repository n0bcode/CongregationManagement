@props([
    'placeholder' => 'Search...',
    'model' => 'search',
    'debounce' => 300,
    'suggestions' => [],
    'showSuggestions' => true,
    'minChars' => 2,
])

<div 
    x-data="enhancedSearch({
        model: '{{ $model }}',
        minChars: {{ $minChars }},
        suggestions: @js($suggestions)
    })"
    {{ $attributes->merge(['class' => 'enhanced-search-wrapper']) }}
>
    <div class="relative">
        <!-- Search Input -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg 
                    x-show="!searching" 
                    class="w-5 h-5 text-slate-400" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <svg 
                    x-show="searching" 
                    x-cloak
                    class="w-5 h-5 text-amber-600 animate-spin" 
                    fill="none" 
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <input 
                x-ref="input"
                type="text"
                wire:model.live.debounce.{{ $debounce }}ms="{{ $model }}"
                x-model="query"
                @input="onInput"
                @keydown="handleKeydown"
                @focus="showDropdown = true"
                @blur.debounce.200ms="showDropdown = false"
                placeholder="{{ $placeholder }}"
                class="form-input w-full min-h-[48px] pl-12 pr-12 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
                autocomplete="off"
            >
            
            <!-- Clear Button -->
            <button 
                x-show="query.length > 0"
                x-cloak
                @click="clearSearch"
                type="button"
                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600"
                title="Clear search"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Suggestions Dropdown -->
        @if($showSuggestions)
        <div 
            x-show="showDropdown && (filteredSuggestions.length > 0 || recentSearches.length > 0)"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="absolute z-50 w-full mt-2 bg-white border border-stone-300 rounded-lg shadow-lg max-h-96 overflow-y-auto"
        >
            <!-- Recent Searches -->
            <div x-show="recentSearches.length > 0 && query.length === 0" class="p-2">
                <div class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">
                    Recent Searches
                </div>
                <template x-for="(search, index) in recentSearches" :key="index">
                    <button
                        @click="selectSuggestion(search)"
                        @mouseenter="selectedIndex = -1"
                        class="w-full flex items-center gap-3 px-3 py-2 text-left hover:bg-stone-50 rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-slate-700" x-text="search"></span>
                    </button>
                </template>
            </div>

            <!-- Suggestions -->
            <div x-show="filteredSuggestions.length > 0" class="p-2">
                <div x-show="query.length > 0" class="px-3 py-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">
                    Suggestions
                </div>
                <template x-for="(suggestion, index) in filteredSuggestions" :key="index">
                    <button
                        @click="selectSuggestion(suggestion)"
                        @mouseenter="selectedIndex = index"
                        :class="{ 'bg-amber-50': selectedIndex === index }"
                        class="w-full flex items-center gap-3 px-3 py-2 text-left hover:bg-stone-50 rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-sm text-slate-700" x-html="highlightMatch(suggestion, query)"></span>
                    </button>
                </template>
            </div>

            <!-- No Results -->
            <div x-show="query.length >= minChars && filteredSuggestions.length === 0 && !searching" class="p-4 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-sm text-slate-600">No suggestions found</p>
                <p class="text-xs text-slate-500 mt-1">Try a different search term</p>
            </div>
        </div>
        @endif

        <!-- Search Tips -->
        <div x-show="query.length > 0 && query.length < minChars" x-cloak class="mt-2 text-xs text-slate-500">
            Type at least {{ $minChars }} characters to search
        </div>
    </div>
</div>
