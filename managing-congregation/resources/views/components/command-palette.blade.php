<div
    x-data="{
        open: false,
        query: '',
        activeIndex: 0,
        items: [
            { id: 'dashboard', title: 'Go to Dashboard', url: '{{ route('dashboard') }}', icon: 'home' },
            { id: 'members', title: 'Manage Members', url: '{{ route('members.index') }}', icon: 'users' },
            { id: 'financials', title: 'Financial Dashboard', url: '{{ route('financials.dashboard') }}', icon: 'currency-dollar' },
            { id: 'profile', title: 'My Profile', url: '{{ route('profile.edit') }}', icon: 'user' },
            // Add more static commands here. Dynamic ones can be fetched via Livewire or API.
        ],
        get filteredItems() {
            if (this.query === '') return this.items;
            return this.items.filter(item => {
                return item.title.toLowerCase().includes(this.query.toLowerCase());
            });
        },
        executeCommand(item) {
            if (item.url) {
                window.location.href = item.url;
            }
            this.open = false;
        }
    }"
    x-init="
        window.addEventListener('keydown', event => {
            if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
                event.preventDefault();
                open = true;
                $nextTick(() => $refs.searchInput.focus());
            }
            if (event.key === 'Escape') {
                open = false;
            }
        });
    "
    x-show="open"
    class="relative z-50"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" @click="open = false"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <div
            class="mx-auto max-w-xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all"
            @click.away="open = false"
        >
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input
                    x-ref="searchInput"
                    type="text"
                    class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                    placeholder="Search..."
                    x-model="query"
                    @keydown.arrow-down.prevent="activeIndex = activeIndex + 1 < filteredItems.length ? activeIndex + 1 : activeIndex"
                    @keydown.arrow-up.prevent="activeIndex = activeIndex - 1 >= 0 ? activeIndex - 1 : activeIndex"
                    @keydown.enter.prevent="executeCommand(filteredItems[activeIndex])"
                >
            </div>

            <ul class="max-h-72 scroll-py-2 overflow-y-auto py-2 text-sm text-gray-800" id="options" role="listbox">
                <template x-for="(item, index) in filteredItems" :key="item.id">
                    <li
                        class="cursor-default select-none px-4 py-2"
                        :class="{ 'bg-indigo-600 text-white': activeIndex === index }"
                        role="option"
                        @click="executeCommand(item)"
                        @mouseenter="activeIndex = index"
                    >
                        <div class="flex items-center">
                            <!-- Icons based on item.icon -->
                            <span class="ml-3 flex-auto truncate" x-text="item.title"></span>
                        </div>
                    </li>
                </template>
                
                <li x-show="filteredItems.length === 0" class="p-4 text-sm text-gray-500">No results found.</li>
            </ul>
        </div>
    </div>
</div>