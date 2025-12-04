<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-3xl font-bold text-slate-800">
                    Good {{ now()->format('A') === 'AM' ? 'Morning' : 'Evening' }}, {{ Auth::user()->name }}
                </h2>
                <p class="text-slate-600 mt-1">{{ now()->format('l, F j, Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Status Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <x-status-card 
                variant="peace" 
                title="Active Members"
                :value="$activeMembersCount"
                description="All communities"
                icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
            />

            <x-status-card 
                variant="attention" 
                title="Needs Attention"
                :value="$needsAttentionCount"
                description="Overdue reminders"
                icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'
            />

            <x-status-card 
                variant="pending" 
                title="Upcoming Events"
                :value="$upcomingReminders->count()"
                description="Next 30 days"
                icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
            />
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <x-card title="Recent Activity" subtitle="Latest updates from your communities">
                    <div class="space-y-0 -mx-6">
                        <x-ledger-row 
                            :date="now()->subDays(1)"
                            description="Sr. Maria entered Novitiate"
                            category="Formation"
                            amount="✓"
                        />
                        <x-ledger-row 
                            :date="now()->subDays(2)"
                            description="Monthly report submitted"
                            category="St. Joseph House"
                            amount="✓"
                        />
                        <x-ledger-row 
                            :date="now()->subDays(3)"
                            description="New member registered"
                            category="Bethany House"
                            amount="✓"
                        />
                    </div>
                </x-card>
            </div>

            <!-- Quick Actions & Reminders -->
            <div>
                <x-card title="Quick Actions">
                    <div class="space-y-3">
                        <x-button variant="primary" href="{{ route('members.create') }}" class="w-full justify-center">
                            Add New Member
                        </x-button>
                        <x-button variant="secondary" href="{{ route('financials.monthly-report') }}" class="w-full justify-center">
                            View Reports
                        </x-button>
                        <x-button variant="secondary" href="{{ route('documents.index') }}" class="w-full justify-center">
                            Manage Documents
                        </x-button>
                    </div>
                </x-card>

                <!-- Upcoming Reminders -->
                <x-card title="Upcoming Reminders" subtitle="Next 30 days" class="mt-6">
                    @if($upcomingReminders->count() > 0)
                        <div class="space-y-0 -mx-6">
                            @foreach($upcomingReminders->take(5) as $reminder)
                                <x-timeline-node
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
                </x-card>
            </div>
        </div>

        <!-- Alerts Section -->
        <div class="mt-8 space-y-4">
            @if($overdueReminders->count() > 0)
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-6">
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
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
