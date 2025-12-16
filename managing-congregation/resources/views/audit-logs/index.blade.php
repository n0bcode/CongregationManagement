<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('audit-logs.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        {{-- User Filter --}}
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-stone-warm-700">
                                User
                            </label>
                            <select id="user_id" name="user_id"
                                class="mt-1 block w-full rounded-md border-stone-warm-300 shadow-sm focus:border-sanctuary-gold focus:ring-sanctuary-gold">
                                <option value="">All Users</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Action Filter --}}
                        <div>
                            <label for="action" class="block text-sm font-medium text-stone-warm-700">
                                Action
                            </label>
                            <select id="action" name="action"
                                class="mt-1 block w-full rounded-md border-stone-warm-300 shadow-sm focus:border-sanctuary-gold focus:ring-sanctuary-gold">
                                <option value="">All Actions</option>
                                @foreach ($actions as $action)
                                    <option value="{{ $action }}"
                                        {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Start Date Filter --}}
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-stone-warm-700">
                                Start Date
                            </label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                class="mt-1 block w-full rounded-md border-stone-warm-300 shadow-sm focus:border-sanctuary-gold focus:ring-sanctuary-gold">
                        </div>

                        {{-- End Date Filter --}}
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-stone-warm-700">
                                End Date
                            </label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                class="mt-1 block w-full rounded-md border-stone-warm-300 shadow-sm focus:border-sanctuary-gold focus:ring-sanctuary-gold">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex gap-3">
                            <button type="submit"
                                class="inline-flex items-center rounded-md bg-sanctuary-gold px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sanctuary-gold-dark focus:outline-none focus:ring-2 focus:ring-sanctuary-gold focus:ring-offset-2">
                                Apply Filters
                            </button>

                            <a href="{{ route('audit-logs.export', request()->only(['start_date', 'end_date'])) }}"
                                class="inline-flex items-center rounded-md border border-stone-warm-300 bg-white px-4 py-2 text-sm font-semibold text-stone-warm-700 shadow-sm hover:bg-stone-warm-50 focus:outline-none focus:ring-2 focus:ring-sanctuary-gold focus:ring-offset-2">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export PDF
                            </a>
                        </div>

                        @if (request()->hasAny(['user_id', 'action', 'start_date', 'end_date']))
                            <a href="{{ route('audit-logs.index') }}"
                                class="text-sm text-stone-warm-600 hover:text-stone-warm-900">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Audit Logs Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-warm-200">
                        <thead class="bg-stone-warm-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-warm-500">
                                    Date & Time
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-warm-500">
                                    User
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-warm-500">
                                    Action
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-warm-500">
                                    Description
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-warm-500">
                                    IP Address
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-warm-200 bg-white">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-stone-warm-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-warm-900">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-warm-900">
                                        {{ $log->user?->name ?? 'System' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                            @if ($log->action === 'created') bg-green-100 text-green-800
                                            @elseif($log->action === 'updated') bg-blue-100 text-blue-800
                                            @elseif($log->action === 'deleted') bg-red-100 text-red-800
                                            @elseif($log->action === 'transferred') bg-purple-100 text-purple-800
                                            @else bg-stone-warm-100 text-stone-warm-800 @endif">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-stone-warm-900">
                                        {{ $log->description }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-warm-500">
                                        {{ $log->ip_address }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-stone-warm-500">
                                        No audit logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($logs->hasPages())
                    <div class="border-t border-stone-warm-200 px-6 py-4">
                        <x-ui.pagination :paginator="$logs" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
