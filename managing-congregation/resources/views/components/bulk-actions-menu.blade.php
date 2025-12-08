@props([
    'selectedCount' => 0,
    'actions' => [], // Array of actions: ['label' => 'Delete', 'method' => 'deleteSelected', 'variant' => 'danger', 'confirm' => 'Are you sure?']
])

<div 
    x-data="bulkActions({{ $selectedCount }})"
    x-show="selectedCount > 0"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform -translate-y-2"
    {{ $attributes->merge(['class' => 'bulk-actions-menu']) }}
>
    <div class="bg-white border border-stone-300 rounded-lg shadow-lg p-4">
        <div class="flex items-center justify-between gap-4">
            <!-- Selection Info -->
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 bg-amber-100 rounded-full">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">
                        <span x-text="selectedCount"></span> item<span x-show="selectedCount !== 1">s</span> selected
                    </p>
                    <p class="text-xs text-slate-600">Choose an action to apply</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                @foreach($actions as $action)
                    @php
                        $variant = $action['variant'] ?? 'secondary';
                        $confirm = $action['confirm'] ?? null;
                        $icon = $action['icon'] ?? null;
                    @endphp
                    
                    <button
                        @if($confirm)
                            @click="if(confirm('{{ $confirm }}')) { executeAction('{{ $action['method'] }}') }"
                        @else
                            @click="executeAction('{{ $action['method'] }}')"
                        @endif
                        :disabled="processing"
                        class="btn-sm btn-{{ $variant }} flex items-center gap-2"
                    >
                        @if($icon)
                            {!! $icon !!}
                        @endif
                        <span>{{ $action['label'] }}</span>
                    </button>
                @endforeach

                <!-- Cancel/Clear Selection -->
                <button
                    @click="$dispatch('clear-selection')"
                    :disabled="processing"
                    class="btn-sm btn-secondary"
                    title="Clear selection"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Progress Bar (shown during processing) -->
        <div x-show="processing" x-cloak class="mt-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-slate-700">Processing...</span>
                <span class="text-sm font-medium text-slate-800">
                    <span x-text="processed"></span> / <span x-text="total"></span>
                </span>
            </div>
            <div class="w-full bg-stone-200 rounded-full h-2">
                <div 
                    class="bg-amber-600 h-2 rounded-full transition-all duration-300"
                    :style="`width: ${progress}%`"
                ></div>
            </div>
            <button
                @click="cancelProcessing"
                class="mt-2 text-sm text-rose-600 hover:text-rose-700 font-medium"
            >
                Cancel
            </button>
        </div>

        <!-- Results (shown after completion) -->
        <div x-show="completed && !processing" x-cloak class="mt-4">
            <div class="flex items-start gap-3 p-3 rounded-lg" 
                 :class="errors.length > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-emerald-50 border border-emerald-200'">
                <svg x-show="errors.length === 0" class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg x-show="errors.length > 0" class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium" :class="errors.length > 0 ? 'text-amber-900' : 'text-emerald-900'">
                        <span x-show="errors.length === 0">Successfully processed <span x-text="processed"></span> item<span x-show="processed !== 1">s</span></span>
                        <span x-show="errors.length > 0">Completed with <span x-text="errors.length"></span> error<span x-show="errors.length !== 1">s</span></span>
                    </p>
                    <div x-show="errors.length > 0" class="mt-2">
                        <p class="text-xs text-amber-800 mb-1">Failed items:</p>
                        <ul class="text-xs text-amber-700 list-disc list-inside">
                            <template x-for="error in errors.slice(0, 5)" :key="error.id">
                                <li x-text="error.message"></li>
                            </template>
                            <li x-show="errors.length > 5" class="text-amber-600">
                                And <span x-text="errors.length - 5"></span> more...
                            </li>
                        </ul>
                    </div>
                </div>
                <button @click="completed = false; errors = []" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
