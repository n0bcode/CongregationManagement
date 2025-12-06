<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $periodicEvent->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ ucfirst($periodicEvent->recurrence) }} Event
                            @if($periodicEvent->community)
                                • {{ $periodicEvent->community->name }}
                            @endif
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-2">Schedule</h4>
                            <p class="text-gray-900">
                                <span class="font-semibold">Start:</span> {{ $periodicEvent->start_date->format('F j, Y') }}
                            </p>
                            <p class="text-gray-900">
                                <span class="font-semibold">End:</span> {{ $periodicEvent->end_date->format('F j, Y') }}
                            </p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-2">Description</h4>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $periodicEvent->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 border-t pt-4">
                        <a href="{{ route('periodic-events.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Back to List</a>
                        <a href="{{ route('periodic-events.edit', $periodicEvent) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Edit Event</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
