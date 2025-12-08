<div>
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="relative">
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-stone-200">
                <div style="width: {{ ($currentStep / $totalSteps) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-sanctuary-blue transition-all duration-500"></div>
            </div>
            <div class="flex justify-between text-xs text-stone-600">
                @foreach($steps as $index => $step)
                    <div class="{{ $currentStep >= $index + 1 ? 'text-sanctuary-blue font-bold' : '' }}">
                        Step {{ $index + 1 }}: {{ $step['label'] }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <form wire:submit.prevent="submit">
        <div class="bg-white shadow-sm rounded-lg p-6 border border-stone-200">
            @foreach($steps as $index => $step)
                <div x-show="$wire.currentStep === {{ $index + 1 }}" x-transition>
                    <h3 class="text-lg font-medium text-stone-900 mb-4">{{ $step['title'] }}</h3>
                    
                    @include($step['view'])
                </div>
            @endforeach
        </div>

        <!-- Navigation -->
        <div class="mt-6 flex justify-between">
            <div>
                @if($currentStep > 1)
                    <x-ui.button type="button" variant="secondary" wire:click="previousStep">
                        Previous
                    </x-ui.button>
                @endif
            </div>
            <div>
                @if($currentStep < $totalSteps)
                    <x-ui.button type="button" variant="primary" wire:click="nextStep">
                        Next
                    </x-ui.button>
                @else
                    <x-ui.button type="submit" variant="primary">
                        Submit
                    </x-ui.button>
                @endif
            </div>
        </div>
    </form>
</div>
