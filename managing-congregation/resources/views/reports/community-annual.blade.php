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
                        <div class="mt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">
                                    Members in {{ $year }} 
                                    <span class="text-gray-500 text-sm font-normal">({{ count($members) }} found)</span>
                                </h3>
                                <button onclick="window.print()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                                    {{ __('Print List') }}
                                </button>
                            </div>

                            @if($members->isEmpty())
                                <p class="text-gray-500 italic text-center py-4">{{ __('No members found for this period.') }}</p>
                            @else
                                <div class="overflow-x-auto">
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
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
