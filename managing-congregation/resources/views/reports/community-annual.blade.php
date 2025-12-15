<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Community Members History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('reports.community-annual') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-forms.input-label for="community_id" :value="__('Select Community')" />
                            <select id="community_id" name="community_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="this.form.submit()">
                                <option value="">{{ __('Select a community...') }}</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ $communityId == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-forms.input-label for="year" :value="__('Year')" />
                            <select id="year" name="year" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="this.form.submit()">
                                @foreach(range(now()->year, now()->year - 20) as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    @if($communityId)
                        <div class="mt-6" x-data="{ viewMode: 'list' }">
                            <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                                <h3 class="text-lg font-semibold">
                                    Members in {{ $year }} 
                                    <span class="text-gray-500 text-sm font-normal">({{ count($members) }} found)</span>
                                </h3>
                                
                                <div class="flex items-center gap-2">
                                    <!-- View Toggle -->
                                    <div class="bg-gray-100 p-1 rounded-lg flex items-center">
                                        <button @click="viewMode = 'list'" 
                                                :class="{ 'bg-white shadow text-gray-900': viewMode === 'list', 'text-gray-500 hover:text-gray-700': viewMode !== 'list' }"
                                                class="p-2 rounded-md transition-all duration-200" title="List View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                            </svg>
                                        </button>
                                        <button @click="viewMode = 'card'"
                                                :class="{ 'bg-white shadow text-gray-900': viewMode === 'card', 'text-gray-500 hover:text-gray-700': viewMode !== 'card' }"
                                                class="p-2 rounded-md transition-all duration-200" title="Card View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <button onclick="window.print()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        <span class="hidden sm:inline">{{ __('Print') }}</span>
                                    </button>
                                </div>
                            </div>

                            @if($members->isEmpty())
                                <p class="text-gray-500 italic text-center py-4">{{ __('No members found for this period.') }}</p>
                            @else
                                <!-- List View -->
                                <div x-show="viewMode === 'list'" class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Religious Name') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Role in Community') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($members as $member)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            @if($member->profile_photo_path)
                                                                <img class="h-8 w-8 rounded-full object-cover mr-2" src="{{ $member->profile_photo_url }}" alt="{{ $member->full_name }}" />
                                                            @endif
                                                            <span class="font-medium text-gray-900">{{ $member->full_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $member->religious_name ?? '-' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-medium">{{ $member->historical_role }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $member->status->color() }}-100 text-{{ $member->status->color() }}-800">
                                                            {{ $member->status->label() }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Card View -->
                                <div x-show="viewMode === 'card'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                    @foreach($members as $member)
                                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow p-5 flex flex-col items-center text-center">
                                            <div class="relative mb-3">
                                                @if($member->profile_photo_path)
                                                    <img class="h-20 w-20 rounded-full object-cover ring-2 ring-white shadow-sm" src="{{ $member->profile_photo_url }}" alt="{{ $member->full_name }}" />
                                                @else
                                                    <div class="h-20 w-20 rounded-full bg-amber-100 flex items-center justify-center ring-2 ring-white shadow-sm">
                                                        <span class="text-amber-600 text-xl font-bold">
                                                            {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <span class="absolute bottom-0 right-0 w-4 h-4 bg-{{ $member->status->color() }}-500 border-2 border-white rounded-full" title="{{ $member->status->label() }}"></span>
                                            </div>

                                            <h4 class="font-semibold text-gray-900 text-lg mb-1 line-clamp-1">{{ $member->full_name }}</h4>
                                            
                                            @if($member->religious_name)
                                                <p class="text-sm text-gray-500 mb-2 italic">"{{ $member->religious_name }}"</p>
                                            @endif

                                            <div class="mt-auto w-full pt-3 border-t border-gray-100 space-y-2">
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-500">{{ __('Role') }}</span>
                                                    <span class="font-medium text-gray-700">{{ $member->historical_role }}</span>
                                                </div>
                                                
                                                <a href="{{ route('members.show', $member) }}" class="block w-full py-2 px-4 bg-stone-50 hover:bg-stone-100 text-stone-700 text-sm font-medium rounded transition-colors mt-3">
                                                    {{ __('View Profile') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
