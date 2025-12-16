<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Demographic Report') }}">
            <x-slot:actions>
                <x-ui.button variant="primary" href="{{ route('reports.demographic.export', request()->query()) }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('Export PDF') }}
                </x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6 mb-6">
                <form method="GET" action="{{ route('reports.demographic') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="community_id" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Community') }}
                            </label>
                            <select id="community_id" name="community_id" class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none">
                                <option value="">{{ __('All Communities') }}</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ $communityId == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-lg font-medium text-slate-700 mb-2">
                                {{ __('Status') }}
                            </label>
                            <select id="status" name="status" class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <x-ui.button type="submit" variant="primary" class="flex-1">
                            {{ __('Apply Filters') }}
                        </x-ui.button>
                        <x-ui.button variant="secondary" href="{{ route('reports.demographic') }}" class="flex-1">
                            {{ __('Clear Filters') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>

            {{-- Summary Card --}}
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg shadow-sm p-8 text-white mb-6">
                <h3 class="text-2xl font-bold mb-2">{{ __('Total Members') }}</h3>
                <p class="text-5xl font-bold">{{ $totalMembers }}</p>
            </div>

            {{-- Charts Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Age Distribution --}}
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
                    <h3 class="text-xl font-semibold text-stone-800 mb-4">{{ __('Age Distribution') }}</h3>
                    <div class="space-y-3">
                        @foreach($ageDistribution as $group => $count)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-slate-700">{{ $group }}</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-stone-200 rounded-full h-3">
                                    <div class="bg-amber-600 h-3 rounded-full" style="width: {{ $totalMembers > 0 ? ($count / $totalMembers * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Status Distribution --}}
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
                    <h3 class="text-xl font-semibold text-stone-800 mb-4">{{ __('Status Distribution') }}</h3>
                    <div class="space-y-3">
                        @foreach($statusDistribution as $statusName => $count)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-slate-700 capitalize">{{ $statusName }}</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-stone-200 rounded-full h-3">
                                    <div class="bg-sanctuary-green h-3 rounded-full" style="width: {{ $totalMembers > 0 ? ($count / $totalMembers * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Community Distribution --}}
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
                    <h3 class="text-xl font-semibold text-stone-800 mb-4">{{ __('Community Distribution') }}</h3>
                    <div class="space-y-3">
                        @foreach($communityDistribution as $communityName => $count)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-slate-700">{{ $communityName }}</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-stone-200 rounded-full h-3">
                                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $totalMembers > 0 ? ($count / $totalMembers * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Formation Stages --}}
                <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
                    <h3 class="text-xl font-semibold text-stone-800 mb-4">{{ __('Formation Stages') }}</h3>
                    <div class="space-y-3">
                        @foreach($formationStages as $stage => $count)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-slate-700 capitalize">{{ str_replace('_', ' ', $stage) }}</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-stone-200 rounded-full h-3">
                                    <div class="bg-purple-600 h-3 rounded-full" style="width: {{ $totalMembers > 0 ? ($count / $totalMembers * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Report Footer --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <p class="text-sm text-blue-800">
                    {{ __('Report generated on') }} {{ now()->format('F d, Y \a\t g:i A') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
