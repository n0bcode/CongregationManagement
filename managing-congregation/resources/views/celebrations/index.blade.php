<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('Celebrations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Upcoming Birthdays (Next 30 Days)</h3>
                        <div class="flex items-center space-x-2">
                            <label for="fontSelector" class="text-sm font-medium text-gray-700">Card Font:</label>
                            <select id="fontSelector" onchange="updateLinks()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="Caveat-VariableFont_wght.ttf">Caveat (Playful)</option>
                                <option value="FleurDeLeah-Regular.ttf">Fleur De Leah (Elegant)</option>
                                <option value="Roboto-Regular.ttf">Roboto (Modern)</option>
                            </select>
                        </div>
                    </div>
                    
                    @if($upcomingBirthdays->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($upcomingBirthdays as $member)
                                <div class="border rounded-lg p-4 flex flex-col items-center">
                                    <img src="{{ $member->profile_photo_url }}" alt="{{ $member->first_name }}" class="w-24 h-24 rounded-full mb-4 object-cover">
                                    <h4 class="text-xl font-bold">{{ $member->first_name }} {{ $member->last_name }}</h4>
                                    <p class="text-gray-600">{{ $member->dob->format('M d') }} (Turning {{ $member->dob->age + 1 }})</p>
                                    
                                    <div class="mt-4 flex space-x-2">
                                        <a href="{{ route('celebrations.birthday.generate', $member) }}?font=Caveat-VariableFont_wght.ttf" target="_blank" class="preview-link px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                                            Preview
                                        </a>
                                        <a href="{{ route('celebrations.birthday.download', $member) }}?font=Caveat-VariableFont_wght.ttf" class="download-link px-3 py-2 bg-gray-200 text-gray-800 text-sm rounded hover:bg-gray-300">
                                            Download
                                        </a>
                                        @if($member->email)
                                            <form action="{{ route('celebrations.birthday.email', $member) }}" method="POST" class="inline email-form">
                                                @csrf
                                                <input type="hidden" name="font" value="Caveat-VariableFont_wght.ttf" class="font-input">
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

                    @if($upcomingBirthdays instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-6">
                            <x-ui.pagination :paginator="$upcomingBirthdays" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateLinks() {
            var font = document.getElementById('fontSelector').value;
            
            // Update Preview Links
            document.querySelectorAll('.preview-link').forEach(function(link) {
                var url = new URL(link.href);
                url.searchParams.set('font', font);
                link.href = url.toString();
            });

            // Update Download Links
            document.querySelectorAll('.download-link').forEach(function(link) {
                var url = new URL(link.href);
                url.searchParams.set('font', font);
                link.href = url.toString();
            });

            // Update Email Forms
            document.querySelectorAll('.font-input').forEach(function(input) {
                input.value = font;
            });
        }
    </script>
</x-app-layout>
