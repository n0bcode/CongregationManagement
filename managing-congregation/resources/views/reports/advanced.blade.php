<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('Advanced Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Skills Distribution -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top Skills Distribution</h3>
                    @if(empty($skillsDistribution))
                        <p class="text-gray-500">No skill data available.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($skillsDistribution as $skill => $count)
                                <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center">
                                    <span class="font-medium text-gray-700">{{ $skill }}</span>
                                    <span class="bg-indigo-100 text-indigo-800 py-1 px-3 rounded-full text-sm font-semibold">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Age Demographics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Age Demographics</h3>
                        @if(empty($ageDemographics))
                            <p class="text-gray-500">No age data available.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($ageDemographics as $group => $count)
                                    <div class="flex items-center">
                                        <div class="w-24 text-sm text-gray-600">{{ $group }}</div>
                                        <div class="flex-1">
                                            <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-500" style="width: {{ ($count / array_sum($ageDemographics)) * 100 }}%"></div>
                                            </div>
                                        </div>
                                        <div class="w-12 text-right text-sm font-semibold text-gray-700">{{ $count }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Upcoming Ordination Anniversaries -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Ordination Anniversaries (30 Days)</h3>
                        @if($upcomingOrdinations->isEmpty())
                            <p class="text-gray-500">No upcoming anniversaries.</p>
                        @else
                            <ul class="divide-y divide-gray-200">
                                @foreach($upcomingOrdinations as $ordination)
                                    <li class="py-3 flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $ordination->member->name }}</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($ordination->step) }} - {{ $ordination->date->format('Y') }}</p>
                                        </div>
                                        <div class="text-sm text-indigo-600 font-semibold">
                                            {{ $ordination->date->format('M j') }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
