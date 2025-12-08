<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Formation Progress</h3>
    
    @if(empty($data['stages']))
        <p class="text-gray-500 text-sm">No members currently in formation.</p>
    @else
        <div class="space-y-4">
            @foreach($data['stages'] as $stage => $count)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $stage)) }}</span>
                        <span class="text-sm text-gray-500">{{ $count }} members</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($count / $data['total_in_formation']) * 100 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 pt-4 border-t border-gray-100">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Total in Formation</span>
                <span class="text-lg font-bold text-gray-900">{{ $data['total_in_formation'] }}</span>
            </div>
        </div>
    @endif
</div>