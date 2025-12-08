<x-ui.card title="Recent Activity" subtitle="Latest updates from your communities">
    <div class="space-y-0 -mx-6">
        @forelse($data['recentActivity'] as $activity)
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
