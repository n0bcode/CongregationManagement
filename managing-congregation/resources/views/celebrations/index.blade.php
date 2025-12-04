<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Celebrations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Upcoming Birthdays (Next 30 Days)</h3>
                    
                    @if($upcomingBirthdays->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($upcomingBirthdays as $member)
                                <div class="border rounded-lg p-4 flex flex-col items-center">
                                    <img src="{{ $member->profile_photo_url }}" alt="{{ $member->first_name }}" class="w-24 h-24 rounded-full mb-4 object-cover">
                                    <h4 class="text-xl font-bold">{{ $member->first_name }} {{ $member->last_name }}</h4>
                                    <p class="text-gray-600">{{ $member->dob->format('M d') }} (Turning {{ $member->dob->age + 1 }})</p>
                                    
                                    <div class="mt-4 flex space-x-2">
                                        <a href="{{ route('celebrations.birthday.generate', $member) }}" target="_blank" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                                            Preview
                                        </a>
                                        <a href="{{ route('celebrations.birthday.download', $member) }}" class="px-3 py-2 bg-gray-200 text-gray-800 text-sm rounded hover:bg-gray-300">
                                            Download
                                        </a>
                                        @if($member->email)
                                            <form action="{{ route('celebrations.birthday.email', $member) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700" onclick="return confirm('Send birthday card to {{ $member->email }}?')">
                                                    Email
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No upcoming birthdays in the next 30 days.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
