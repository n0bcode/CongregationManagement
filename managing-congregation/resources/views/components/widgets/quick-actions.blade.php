<x-ui.card title="System Shortcuts">
    <!-- System Modules Grid -->
    <div class="grid grid-cols-2 gap-4 mb-4">
        <!-- Finance Module -->
        <a href="{{ route('financials.dashboard') }}" class="block group relative bg-white p-3 rounded-xl border border-stone-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200">
            <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="flex flex-col h-full justify-between relative z-10">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center mb-2 group-hover:bg-emerald-200 transition-colors">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="font-semibold text-stone-800 text-sm">Finance</h4>
                    <p class="text-[10px] text-stone-500">Expenses</p>
                </div>
            </div>
        </a>

        <!-- Reports Module -->
        <a href="{{ route('reports.demographic') }}" class="block group relative bg-white p-3 rounded-xl border border-stone-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200">
            <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div class="flex flex-col h-full justify-between relative z-10">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mb-2 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <h4 class="font-semibold text-stone-800 text-sm">Reports</h4>
                    <p class="text-[10px] text-stone-500">Analytics</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Secondary Actions -->
    <div class="space-y-3">
        <x-ui.button variant="primary" href="{{ route('members.create') }}" class="w-full justify-center">
            {{ __('Add New Member') }}
        </x-ui.button>
        
        <div class="grid grid-cols-2 gap-2">
             <x-ui.button variant="secondary" href="{{ route('financials.index') }}" class="justify-center text-xs">
                {{ __('Expenses') }}
            </x-ui.button>
             <x-ui.button variant="secondary" href="{{ route('reports.builder') }}" class="justify-center text-xs">
                {{ __('Report Builder') }}
            </x-ui.button>
             <x-ui.button variant="secondary" href="{{ route('documents.index') }}" class="justify-center text-xs">
                {{ __('Documents') }}
            </x-ui.button>
             <x-ui.button variant="secondary" href="{{ route('reports.advanced') }}" class="justify-center text-xs">
                {{ __('Advanced') }}
            </x-ui.button>
        </div>
    </div>
</x-ui.card>
