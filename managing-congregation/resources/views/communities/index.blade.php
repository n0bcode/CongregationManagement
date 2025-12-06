<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-slate-800">
                {{ __('Communities') }}
            </h2>
            @can('create', App\Models\Community::class)
                <a href="{{ route('communities.create') }}" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500 transition-all shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Create New Community') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-lg">
                    {{ session('error') }}
                </div>
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
                        <button type="submit" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 bg-white text-slate-700 font-medium rounded-lg border-2 border-stone-300 hover:border-amber-600 hover:text-amber-600 focus:outline-none focus:ring-4 focus:ring-amber-500 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            {{ __('Search') }}
                        </button>
                        @if(request('search'))
                            <a href="{{ route('communities.index') }}" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 bg-white text-slate-700 font-medium rounded-lg border-2 border-stone-300 hover:border-stone-400 focus:outline-none focus:ring-4 focus:ring-stone-300 transition-all">
                                {{ __('Clear') }}
                            </a>
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
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">
                                            {{ $community->members_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-base text-slate-600">
                                        {{ $community->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            @can('update', $community)
                                                <a href="{{ route('communities.edit', $community) }}" 
                                                   class="inline-flex items-center text-amber-600 hover:text-amber-700 font-medium transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    {{ __('Edit') }}
                                                </a>
                                            @endcan
                                            @can('delete', $community)
                                                <form method="POST" 
                                                      action="{{ route('communities.destroy', $community) }}" 
                                                      class="inline"
                                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this community? This action cannot be undone.') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center text-rose-600 hover:text-rose-700 font-medium transition-colors">
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
            @if($communities->hasPages())
                <div class="mt-6">
                    {{ $communities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
