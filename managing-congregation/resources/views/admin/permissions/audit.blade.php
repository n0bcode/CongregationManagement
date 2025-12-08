<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Permission Audit Log') }}
            </h2>
            <a href="{{ route('admin.permissions.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-stone-300 rounded-lg text-slate-700 hover:bg-stone-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back to Permissions') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-stone-200">
            {{-- Header --}}
            <div class="px-8 py-6 border-b border-stone-200">
                <h3 class="text-lg font-medium text-slate-800">
                    {{ __('Recent Permission Changes') }}
                </h3>
                <p class="mt-1 text-sm text-slate-600">
                    {{ __('Track all permission and role changes made by administrators.') }}
                </p>
            </div>

            {{-- Audit Log Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-stone-50 border-b border-stone-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-slate-700">
                                {{ __('Date & Time') }}
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-slate-700">
                                {{ __('Action') }}
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-slate-700">
                                {{ __('Target') }}
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-slate-700">
                                {{ __('User') }}
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-slate-700">
                                {{ __('Details') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-stone-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-700 whitespace-nowrap">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($log->action === 'permission_updated')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ __('Permission Updated') }}
                                        </span>
                                    @elseif ($log->action === 'role_changed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ __('Role Changed') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-stone-100 text-slate-800">
                                            {{ $log->action }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    <div class="font-medium">{{ ucfirst($log->target_type ?? 'N/A') }}</div>
                                    <div class="text-xs text-slate-500">{{ $log->target_id ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    {{ $log->user?->name ?? __('System') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    @if ($log->action === 'permission_updated' && isset($log->changes['permissions']))
                                        <details class="cursor-pointer">
                                            <summary class="text-amber-600 hover:text-amber-700">
                                                {{ count($log->changes['permissions']) }} {{ __('permissions') }}
                                            </summary>
                                            <div class="mt-2 p-3 bg-stone-50 rounded text-xs max-h-40 overflow-y-auto">
                                                <ul class="list-disc list-inside space-y-1">
                                                    @foreach ($log->changes['permissions'] as $permission)
                                                        <li>{{ $permission }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </details>
                                    @elseif ($log->action === 'role_changed')
                                        <span class="text-slate-600">
                                            {{ $log->changes['old_role'] ?? 'N/A' }} 
                                            <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                            {{ $log->changes['new_role'] ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">{{ __('No details') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-4 text-lg">{{ __('No audit logs found') }}</p>
                                    <p class="mt-2 text-sm">{{ __('Permission changes will appear here.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($logs->hasPages())
                <div class="px-8 py-6 border-t border-stone-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
