@props(['tabs' => []])

<div x-data="{ activeTab: '{{ array_key_first($tabs) }}' }" class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden">
    <!-- Tabs Header -->
    <div class="border-b border-stone-200 bg-stone-50">
        <nav class="flex -mb-px" aria-label="Tabs">
            @foreach($tabs as $key => $label)
                <button 
                    @click="activeTab = '{{ $key }}'"
                    :class="{ 'border-amber-500 text-amber-600': activeTab === '{{ $key }}', 'border-transparent text-stone-500 hover:text-stone-700 hover:border-stone-300': activeTab !== '{{ $key }}' }"
                    class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200"
                >
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Tabs Content -->
    <div class="p-6">
        @foreach($tabs as $key => $label)
            <div x-show="activeTab === '{{ $key }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                {{ ${$key} ?? '' }}
            </div>
        @endforeach
    </div>
</div>
