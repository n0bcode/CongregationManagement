<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Communities') }}">
            <x-slot:actions>
                @can('create', App\Models\Community::class)
                    <x-ui.button variant="primary" href="{{ route('communities.create') }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('Create New Community') }}
                    </x-ui.button>
                @endcan
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <x-ui.alert variant="success" class="mb-6">
                    {{ session('success') }}
                </x-ui.alert>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <x-ui.alert variant="danger" class="mb-6">
                    {{ session('error') }}
                </x-ui.alert>
            @endif

            <!-- Search Box -->
            <div class="mb-6">
                <form method="GET" action="{{ route('communities.index') }}">
                    <div class="flex gap-4">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="{{ __('Search by community name...') }}"
                               class="flex-1 min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none">
                        <x-ui.button type="submit" variant="secondary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            {{ __('Search') }}
                        </x-ui.button>
                        @if(request('search'))
                            <x-ui.button href="{{ route('communities.index') }}" variant="secondary">
                                {{ __('Clear') }}
                            </x-ui.button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Communities Table -->
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                                    {{ __('Name') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                                    {{ __('Location') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-sm font-semibold text-slate-700">
                                    {{ __('Members') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                                    {{ __('Created Date') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-sm font-semibold text-slate-700">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-stone-200">
                            @forelse($communities as $community)
                                <tr class="hover:bg-stone-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('communities.show', $community) }}" 
                                           class="text-lg font-semibold text-amber-600 hover:text-amber-700 transition-colors">
                                            {{ $community->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-base text-slate-600">
                                        {{ $community->location ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <x-ui.badge variant="primary" size="md">
                                            {{ $community->members_count }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-6 py-4 text-base text-slate-600">
                                        {{ $community->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            @can('update', $community)
                                                <x-ui.action-link href="{{ route('communities.edit', $community) }}" variant="primary">
                                                    <x-slot:icon>
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </x-slot:icon>
                                                    {{ __('Edit') }}
                                                </x-ui.action-link>
                                            @endcan
                                            @can('delete', $community)
                                                <form method="POST" 
                                                      action="{{ route('communities.destroy', $community) }}" 
                                                      class="inline"
                                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this community? This action cannot be undone.') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center text-rose-600 hover:text-rose-700 font-medium transition-colors focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-rose-500 rounded">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-stone-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <p class="text-lg text-slate-500 mb-2">
                                                {{ request('search') ? __('No communities found matching your search.') : __('No communities yet.') }}
                                            </p>
                                            @can('create', App\Models\Community::class)
                                                @if(!request('search'))
                                                    <a href="{{ route('communities.create') }}" class="text-amber-600 hover:text-amber-700 font-medium">
                                                        {{ __('Create your first community') }}
                                                    </a>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <x-ui.pagination :paginator="$communities" />
            </div>
    </div>
</x-app-layout>
