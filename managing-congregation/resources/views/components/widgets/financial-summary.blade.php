<x-ui.status-card
    variant="attention"
    title="Total Expenses"
    :value="'$' . number_format($data['total_expenses'], 2)"
    description="Current fiscal year expenses"
>
    <x-slot name="icon">
        <svg class="w-full h-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </x-slot>

    <div class="mt-4 pt-4 border-t border-rose-200">
        <a href="#" class="text-sm font-medium text-rose-700 hover:text-rose-800 flex items-center">
            View financial report
            <svg class="ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</x-ui.status-card>
