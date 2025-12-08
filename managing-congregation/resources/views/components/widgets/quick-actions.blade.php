<x-ui.card title="Quick Actions">
    <div class="space-y-3">
        <x-ui.button variant="primary" href="{{ route('members.create') }}" class="w-full justify-center">
            Add New Member
        </x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('financials.monthly-report') }}" class="w-full justify-center">
            View Reports
        </x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('documents.index') }}" class="w-full justify-center">
            Manage Documents
        </x-ui.button>
    </div>
</x-ui.card>
