<x-ui.status-card
    variant="peace"
    title="Total Members"
    :value="$data['total']"
    description="Active members in your community"
>
    <x-slot name="icon">
        <svg class="w-full h-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
    </x-slot>
    
    <div class="mt-4 pt-4 border-t border-emerald-200">
        <a href="{{ route('members.index') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800 flex items-center">
            View all members
            <svg class="ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</x-ui.status-card>
