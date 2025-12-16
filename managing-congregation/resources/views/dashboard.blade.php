<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header>
            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Good {{ now()->format('A') === 'AM' ? 'Morning' : 'Evening' }}, {{ Auth::user()->name }}
                </h1>
                <p class="text-base text-slate-600 mt-1">{{ now()->format('l, F j, Y') }}</p>
            </div>
        </x-ui.page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Status Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <x-ui.status-card 
                variant="peace" 
                title="Active Members"
                :value="$activeMembersCount"
                description="All communities"
            >
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </x-slot:icon>
            </x-ui.status-card>

            <x-ui.status-card 
                variant="attention" 
                title="Needs Attention"
                :value="$needsAttentionCount"
                description="Overdue reminders"
            >
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </x-slot:icon>
            </x-ui.status-card>

            <x-ui.status-card 
                variant="pending" 
                title="Upcoming Events"
                :value="$upcomingReminders->count()"
                description="Next 30 days"
            >
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </x-slot:icon>
            </x-ui.status-card>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <x-ui.card title="Recent Activity" subtitle="Latest updates from your communities">
                    <div class="space-y-0 -mx-6">
                        @forelse($recentActivity as $activity)
                            <x-features.ledger-row 
                                :date="$activity->created_at"
                                :description="$activity->description"
                                :category="ucfirst(str_replace('_', ' ', $activity->target_type ?? 'System'))"
                                :amount="$activity->user ? $activity->user->name : 'System'"
                            />
                        @empty
                            <div class="px-6 py-4 text-sm text-gray-500 text-center">
                                No recent activity found.
                            </div>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <!-- Quick Actions & Reminders -->
            <div>
                <!-- System Modules Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Finance Module -->
                    <a href="{{ route('financials.dashboard') }}" class="block group relative bg-white p-4 rounded-xl border border-stone-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200">
                        <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-16 h-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="flex flex-col h-full justify-between relative z-10">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition-colors">
                                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-stone-800">Finance</h4>
                                <p class="text-xs text-stone-500 mt-1">Expenses & Budget</p>
                            </div>
                        </div>
                    </a>

                    <!-- Reports Module -->
                    <a href="{{ route('reports.demographic') }}" class="block group relative bg-white p-4 rounded-xl border border-stone-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200">
                        <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div class="flex flex-col h-full justify-between relative z-10">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-colors">
                                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-stone-800">Reports</h4>
                                <p class="text-xs text-stone-500 mt-1">Data & Analytics</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Secondary Actions -->
                <div class="mt-4 grid grid-cols-2 gap-4">
                     <a href="{{ route('members.create') }}" class="flex items-center justify-center px-4 py-3 bg-stone-50 hover:bg-stone-100 border border-stone-200 rounded-xl text-sm font-medium text-stone-700 transition-colors">
                        <svg class="w-5 h-5 mr-2 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        {{ __('Add Member') }}
                    </a>
                    <a href="{{ route('documents.index') }}" class="flex items-center justify-center px-4 py-3 bg-stone-50 hover:bg-stone-100 border border-stone-200 rounded-xl text-sm font-medium text-stone-700 transition-colors">
                        <svg class="w-5 h-5 mr-2 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        {{ __('Documents') }}
                    </a>
                </div>

                <!-- Upcoming Reminders -->
                <x-ui.card title="Upcoming Reminders" subtitle="Next 30 days" class="mt-6">
                    @if($upcomingReminders->count() > 0)
                        <div class="space-y-0 -mx-6">
                            @foreach($upcomingReminders->take(5) as $reminder)
                                <x-features.timeline-node
                                    :date="$reminder->reminder_date"
                                    :title="$reminder->title"
                                    :description="$reminder->description"
                                    :state="$reminder->reminder_date->isToday() ? 'today' : ($reminder->reminder_date->isPast() ? 'past' : 'future')"
                                />
                            @endforeach
                        </div>
                        @if($upcomingReminders->count() > 5)
                            <div class="mt-4 text-center">
                                <p class="text-sm text-slate-600">
                                    {{ $upcomingReminders->count() - 5 }} more reminders...
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-slate-600">No upcoming reminders</p>
                        </div>
                    @endif
                </x-ui.card>
            </div>
        </div>

        <!-- Alerts Section -->
        <div class="mt-8 space-y-4">
            @if($overdueReminders->count() > 0)
                <x-ui.alert variant="danger">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold text-rose-900">
                                {{ $overdueReminders->count() }} Overdue {{ Str::plural('Reminder', $overdueReminders->count()) }}
                            </h3>
                            <div class="mt-2 text-sm text-rose-800">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($overdueReminders->take(3) as $reminder)
                                        <li>
                                            <strong>{{ $reminder->title }}</strong>
                                            - Due {{ $reminder->reminder_date->diffForHumans() }}
                                        </li>
                                    @endforeach
                                    @if($overdueReminders->count() > 3)
                                        <li class="text-rose-700">
                                            And {{ $overdueReminders->count() - 3 }} more...
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </x-ui.alert>
            @endif
        </div>
    </div>
</x-app-layout>
