@props(['submitLabel' => 'Submit'])

<div class="mt-8 pt-5 border-t border-gray-200">
    <div class="flex justify-between">
        <button 
            type="button" 
            x-show="currentStep > 1" 
            @click="prevStep()"
            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Previous
        </button>

        <div class="flex-1"></div>

        <button 
            type="button" 
            x-show="currentStep < totalSteps" 
            @click="nextStep()"
            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Next
        </button>

        <button 
            type="submit" 
            x-show="currentStep === totalSteps" 
            :disabled="isSubmitting"
            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
            <span x-show="isSubmitting" class="mr-2">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            {{ $submitLabel }}
        </button>
    </div>
    
    <!-- Progress Indicator -->
    <div class="mt-4 flex justify-center">
        <template x-for="step in totalSteps">
            <div class="mx-1 w-2 h-2 rounded-full" :class="step === currentStep ? 'bg-indigo-600' : 'bg-gray-300'"></div>
        </template>
    </div>
</div>
