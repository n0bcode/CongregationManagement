@props([
    'paginator',
    'showPageSize' => true,
    'showJumpTo' => true,
    'showInfiniteScroll' => false,
    'pageSizeOptions' => [10, 25, 50, 100],
    'scrollContainer' => 'window',
])

<div 
    x-data="smartPagination({
        currentPage: {{ $paginator->currentPage() }},
        lastPage: {{ $paginator->lastPage() }},
        perPage: {{ $paginator->perPage() }},
        total: {{ $paginator->total() }},
        showInfiniteScroll: {{ $showInfiniteScroll ? 'true' : 'false' }},
        scrollContainer: '{{ $scrollContainer }}'
    })"
    {{ $attributes->merge(['class' => 'smart-pagination']) }}
>
    <!-- Pagination Info & Controls -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 py-4">
        <!-- Left: Info & Page Size -->
        <div class="flex items-center gap-4">
            <!-- Pagination Info -->
            <div class="text-sm text-slate-600">
                Showing 
                <span class="font-medium text-slate-800">{{ $paginator->firstItem() ?? 0 }}</span>
                to 
                <span class="font-medium text-slate-800">{{ $paginator->lastItem() ?? 0 }}</span>
                of 
                <span class="font-medium text-slate-800">{{ $paginator->total() }}</span>
                results
            </div>

            <!-- Page Size Selector -->
            @if($showPageSize)
            <div class="flex items-center gap-2">
                <label for="pageSize" class="text-sm text-slate-600">Per page:</label>
                <select 
                    id="pageSize"
                    x-model="perPage"
                    @change="changePageSize"
                    class="form-select min-h-[48px] px-3 py-2 text-sm text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
                >
                    @foreach($pageSizeOptions as $size)
                        <option value="{{ $size }}" {{ $paginator->perPage() == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <!-- Center: Page Navigation -->
        <div class="flex items-center gap-2">
            <!-- First Page -->
            <button
                @click="goToPage(1)"
                :disabled="currentPage === 1"
                class="btn-sm btn-secondary min-h-[48px] px-3"
                title="First page"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Previous Page -->
            <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="btn-sm btn-secondary min-h-[48px] px-3"
                title="Previous page"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Page Numbers -->
            <div class="hidden md:flex items-center gap-1">
                <template x-for="page in visiblePages" :key="page">
                    <button
                        x-show="page !== '...'"
                        @click="goToPage(page)"
                        :class="page === currentPage ? 'bg-amber-600 text-white' : 'bg-white text-slate-700 hover:bg-stone-50'"
                        class="min-h-[48px] min-w-[48px] px-3 py-2 text-sm font-medium border border-stone-300 rounded-lg transition-colors focus:ring-4 focus:ring-amber-500 focus:outline-none"
                        x-text="page"
                    ></button>
                    <span x-show="page === '...'" class="px-2 text-slate-400">...</span>
                </template>
            </div>

            <!-- Mobile: Current Page Display -->
            <div class="md:hidden flex items-center gap-2 px-4 py-2 bg-white border border-stone-300 rounded-lg">
                <span class="text-sm font-medium text-slate-800" x-text="currentPage"></span>
                <span class="text-sm text-slate-600">/</span>
                <span class="text-sm text-slate-600" x-text="lastPage"></span>
            </div>

            <!-- Next Page -->
            <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === lastPage"
                class="btn-sm btn-secondary min-h-[48px] px-3"
                title="Next page"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Last Page -->
            <button
                @click="goToPage(lastPage)"
                :disabled="currentPage === lastPage"
                class="btn-sm btn-secondary min-h-[48px] px-3"
                title="Last page"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!-- Right: Jump to Page -->
        @if($showJumpTo)
        <div class="flex items-center gap-2">
            <label for="jumpToPage" class="text-sm text-slate-600 whitespace-nowrap">Go to:</label>
            <input
                id="jumpToPage"
                type="number"
                x-model="jumpToPageInput"
                @keydown.enter="jumpToPage"
                min="1"
                :max="lastPage"
                placeholder="Page"
                class="form-input w-20 min-h-[48px] px-3 py-2 text-sm text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none"
            >
            <button
                @click="jumpToPage"
                class="btn-sm btn-secondary min-h-[48px] px-3"
                title="Go to page"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </div>
        @endif
    </div>

    <!-- Infinite Scroll Trigger -->
    @if($showInfiniteScroll)
    <div 
        x-intersect="loadMore"
        class="py-4 text-center"
    >
        <div x-show="loading" class="flex items-center justify-center gap-2 text-slate-600">
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm">Loading more...</span>
        </div>
        <div x-show="!loading && currentPage < lastPage" class="text-sm text-slate-500">
            Scroll down to load more
        </div>
        <div x-show="currentPage === lastPage" class="text-sm text-slate-500">
            No more results
        </div>
    </div>
    @endif
</div>
