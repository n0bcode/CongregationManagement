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
