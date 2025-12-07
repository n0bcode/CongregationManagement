<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Toolbar -->
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                <!-- Search & Filters -->
                <div class="flex flex-col md:flex-row gap-4 flex-grow">
                    <div class="relative flex-grow max-w-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search members...">
                    </div>

                    <select wire:model.live="communityId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">All Communities</option>
                        @foreach($communities as $community)
                            <option value="{{ $community->id }}">{{ $community->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->name }}</option>
                        @endforeach
                    </select>

                    <!-- Presets -->
                    <div class="flex items-center gap-2">
                        <button wire:click="applyPreset('active')" class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">Active Only</button>
                        <button wire:click="$set('status', null)" class="text-xs text-gray-500 hover:text-gray-700 underline">Clear</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    @if(count($selected) > 0)
                        <button wire:click="deleteSelected" wire:confirm="Are you sure you want to delete selected members?" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            Delete ({{ count($selected) }})
                        </button>
                    @endif
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                    <input wire:model.live="selectAll" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                            </th>
                            <th scope="col" class="py-3 px-6 cursor-pointer" wire:click="sortBy('first_name')">
                                Name
                                @if($sortField === 'first_name')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-6 cursor-pointer" wire:click="sortBy('religious_name')">
                                Religious Name
                                @if($sortField === 'religious_name')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-6 cursor-pointer" wire:click="sortBy('status')">
                                Status
                                @if($sortField === 'status')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-6">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="p-4 w-4">
                                    <div class="flex items-center">
                                        <input wire:model.live="selected" value="{{ $member->id }}" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $member->first_name }} {{ $member->last_name }}
                                </td>
                                <td class="py-4 px-6">
                                    {{ $member->religious_name ?? '-' }}
                                </td>
                                <td class="py-4 px-6">
                                    <div x-data="{ isEditing: false, value: '{{ $member->status->value }}' }" class="relative">
                                        <div x-show="!isEditing" 
                                             @click="isEditing = true; $nextTick(() => $refs.select.focus())" 
                                             class="cursor-pointer hover:bg-gray-100 p-1 rounded transition-colors duration-200">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $member->status->name }}
                                            </span>
                                        </div>
                                        <div x-show="isEditing" style="display: none;">
                                            <select x-ref="select" 
                                                    x-model="value" 
                                                    @change="$wire.updateMember({{ $member->id }}, 'status', value); isEditing = false" 
                                                    @blur="isEditing = false"
                                                    class="block w-full p-1 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                                                @foreach(\App\Enums\MemberStatus::cases() as $status)
                                                    <option value="{{ $status->value }}">{{ $status->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <a href="{{ route('members.show', $member) }}" class="font-medium text-blue-600 hover:underline">View</a>
                                    <a href="{{ route('members.edit', $member) }}" class="font-medium text-blue-600 hover:underline ml-3">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-6 text-center">No members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $members->links() }}
            </div>
        </div>
    </div>
</div>
