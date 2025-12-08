@props(['name', 'title'])

<div
    x-data="{ show: false }"
    x-on:open-bottom-sheet-{{ $name }}.window="show = true"
    x-on:close-bottom-sheet-{{ $name }}.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="relative z-50"
    aria-labelledby="slide-over-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <!-- Background backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-in-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="show = false"
    ></div>

    <div class="fixed inset-x-0 bottom-0 overflow-hidden">
        <div class="pointer-events-none fixed inset-x-0 bottom-0 flex max-w-full pl-10">
            <div
                x-show="show"
                x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="pointer-events-auto w-screen max-w-md mx-auto"
            >
                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl rounded-t-xl">
                    <div class="px-4 py-6 sm:px-6">
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">{{ $title }}</h2>
                            <div class="ml-3 flex h-7 items-center">
                                <button type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @click="show = false">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="relative mt-6 flex-1 px-4 sm:px-6 pb-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>