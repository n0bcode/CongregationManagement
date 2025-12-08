<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-stone-800">
                {{ $community->name }}
            </h2>
            <div class="flex gap-3">
                @can('update', $community)
                    <x-button variant="primary" href="{{ route('communities.edit', $community) }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('Edit') }}
                    </x-button>
                @endcan
                @can('delete', $community)
                    <form method="POST" 
                          action="{{ route('communities.destroy', $community) }}" 
                          class="inline"
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this community? This action cannot be undone.') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center min-h-[48px] px-6 py-3 bg-white text-rose-600 font-medium rounded-lg border-2 border-rose-300 hover:bg-rose-50 hover:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-300 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endcan
            </div>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Community Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
                        <h3 class="text-xl font-semibold text-slate-800 mb-6">{{ __('Community Information') }}</h3>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-slate-500 mb-1">{{ __('Name') }}</dt>
                                <dd class="text-lg font-semibold text-slate-800">{{ $community->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-slate-500 mb-1">{{ __('Location') }}</dt>
                                <dd class="text-base text-slate-700">{{ $community->location ?? '—' }}</dd>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-stone-200">
                                <div>
                                    <dt class="text-sm font-medium text-slate-500 mb-1">{{ __('Created Date') }}</dt>
                                    <dd class="text-base text-slate-700">{{ $community->created_at->format('F d, Y') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-slate-500 mb-1">{{ __('Last Updated') }}</dt>
                                    <dd class="text-base text-slate-700">{{ $community->updated_at->format('F d, Y') }}</dd>
                                </div>
                            </div>
                        </dl>
                    </div>

                    <!-- Recent Members Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold text-slate-800">{{ __('Recent Members') }}</h3>
                            @if($community->members_count > 0)
                                <a href="{{ route('members.index', ['community_id' => $community->id]) }}" class="text-amber-600 hover:text-amber-700 font-medium text-sm">
                                    {{ __('View All') }} →
                                </a>
                            @endif
                        </div>

                        @if($recentMembers->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentMembers as $member)
                                    <div class="flex items-center justify-between p-4 bg-stone-50 rounded-lg hover:bg-stone-100 transition-colors">
                                        <div class="flex items-center gap-4">
                                            @if($member->profile_photo_path)
                                                <img src="{{ Storage::url($member->profile_photo_path) }}" 
                                                     alt="{{ $member->first_name }}"
                                                     class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center border-2 border-white shadow-sm">
                                                    <span class="text-amber-600 font-semibold text-lg">
                                                        {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('members.show', $member) }}" class="text-base font-semibold text-slate-800 hover:text-amber-600 transition-colors">
                                                    {{ $member->first_name }} {{ $member->last_name }}
                                                </a>
                                                <p class="text-sm text-slate-500">
                                                    {{ __('Joined') }} {{ $member->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('members.show', $member) }}" class="text-amber-600 hover:text-amber-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            @if($community->members_count > 10)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('members.index', ['community_id' => $community->id]) }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 font-medium">
                                        {{ __('View all :count members', ['count' => $community->members_count]) }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-stone-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-base text-slate-500 mb-4">{{ __('No members in this community yet.') }}</p>
                                @can('create', App\Models\Member::class)
                                    <a href="{{ route('members.create', ['community_id' => $community->id]) }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 font-medium">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        {{ __('Add First Member') }}
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics Sidebar -->
                <div class="space-y-6">
                    <!-- Member Count Card -->
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow-lg p-8 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">{{ __('Total Members') }}</h3>
                            <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="text-5xl font-bold mb-2">{{ $community->members_count }}</p>
                        @if($community->members_count > 0)
                            <a href="{{ route('members.index', ['community_id' => $community->id]) }}" class="inline-flex items-center text-amber-100 hover:text-white text-sm font-medium">
                                {{ __('View All Members') }}
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">{{ __('Quick Actions') }}</h3>
                        <div class="space-y-3">
                            @can('create', App\Models\Member::class)
                                <a href="{{ route('members.create', ['community_id' => $community->id]) }}" class="flex items-center justify-between p-3 bg-stone-50 hover:bg-stone-100 rounded-lg transition-colors group">
                                    <span class="text-slate-700 font-medium group-hover:text-amber-600">{{ __('Add Member') }}</span>
                                    <svg class="w-5 h-5 text-slate-400 group-hover:text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endcan
                            <a href="{{ route('communities.index') }}" class="flex items-center justify-between p-3 bg-stone-50 hover:bg-stone-100 rounded-lg transition-colors group">
                                <span class="text-slate-700 font-medium group-hover:text-amber-600">{{ __('All Communities') }}</span>
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
