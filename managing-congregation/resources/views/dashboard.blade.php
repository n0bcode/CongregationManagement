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
                value="127"
                description="All communities"
                icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
            />

            <x-status-card 
                variant="attention" 
                title="Needs Attention"
                value="3"
                description="Upcoming vow renewals"
                icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'
            />

            <x-status-card 
                variant="pending" 
                title="Pending Reports"
                value="5"
                description="Monthly financial reports"
                icon='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
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

            <!-- Quick Actions -->
            <div>
                <x-card title="Quick Actions">
                    <div class="space-y-3">
                        <x-button variant="primary" class="w-full justify-center">
                            Add New Member
                        </x-button>
                        <x-button variant="secondary" class="w-full justify-center">
                            View Reports
                        </x-button>
                        <x-button variant="secondary" class="w-full justify-center">
                            Manage Communities
                        </x-button>
                    </div>
                </x-card>

                <!-- Upcoming Events -->
                <x-card title="Upcoming Feasts" class="mt-6">
                    <x-feast-timeline :events="[
                        (object)['date' => now()->addDays(2), 'name' => 'St. Francis'],
                        (object)['date' => now()->addDays(5), 'name' => 'St. Teresa'],
                        (object)['date' => now()->addDays(10), 'name' => 'All Saints'],
                    ]" />
                </x-card>
            </div>
        </div>

        <!-- Alerts Section -->
        <div class="mt-8 space-y-4">
            <x-alert type="info" title="System Update">
                The system will undergo maintenance on Sunday, {{ now()->next('Sunday')->format('F j') }} from 2:00 AM to 4:00 AM.
            </x-alert>
        </div>
    </div>
</x-app-layout>
