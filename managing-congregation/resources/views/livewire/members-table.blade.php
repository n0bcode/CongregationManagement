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
                <!-- Search & Filters -->
                <div class="flex flex-col lg:flex-row gap-4 flex-grow mb-6 lg:mb-0">
                    <!-- Search Input -->
                    <div class="relative flex-grow max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Search members by name, status, community..." 
                            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                    </div>

                    <!-- Community Dropdown -->
                    <select wire:model.live="communityId" class="block w-full lg:w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-700">
                        <option value="">All Communities</option>
                        @foreach($communities as $community)
                            <option value="{{ $community->id }}">{{ $community->name }}</option>
                        @endforeach
                    </select>

                    <!-- Status Dropdown -->
                    <select wire:model.live="status" class="block w-full lg:w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-700">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->name }}</option>
                        @endforeach
                    </select>

                    <!-- Filters Actions -->
                    <div class="flex items-center gap-3">
                        <button 
                            wire:click="resetFilters" 
                            class="text-sm text-gray-500 hover:text-gray-900 underline whitespace-nowrap"
                        >
                            {{ __('Clear Filters') }}
                        </button>
                    </div>
                </div>

                <!-- Bulk Actions Menu -->
                <x-tables.bulk-actions-menu
                    :selectedCount="count($selected)"
                    :actions="[
                        [
                            'label' => 'Delete Selected',
                            'method' => 'deleteSelected',
                            'variant' => 'danger',
                            'confirm' => 'Are you sure you want to delete the selected members? This action cannot be undone.',
                            'icon' => '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\'/></svg>'
                        ],
                        [
                            'label' => 'Export Selected',
                            'method' => 'exportSelected',
                            'variant' => 'secondary',
                            'icon' => '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'/></svg>'
                        ]
                    ]"
                    class="mb-4"
                    @clear-selection.window="selected = []; selectAll = false"
                />
            </div>

            <!-- Responsive Table -->
            <x-tables.responsive-table>
                <x-slot name="thead">
                    <tr>
                        <th scope="col" class="p-4 w-4">
                            <div class="flex items-center">
                                <input wire:model.live="selectAll" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                        </th>
                        <th scope="col" class="py-3 px-6 cursor-pointer text-xs font-medium text-gray-500 uppercase tracking-wider" wire:click="sortBy('first_name')">
                            Name
                            @if($sortField === 'first_name')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="py-3 px-6 cursor-pointer text-xs font-medium text-gray-500 uppercase tracking-wider" wire:click="sortBy('religious_name')">
                            Religious Name
                            @if($sortField === 'religious_name')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="py-3 px-6 cursor-pointer text-xs font-medium text-gray-500 uppercase tracking-wider" wire:click="sortBy('status')">
                            Status
                            @if($sortField === 'status')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </x-slot>

                <x-slot name="tbody">
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
                                <x-forms.inline-edit
                                    :id="$member->id"
                                    field="religious_name"
                                    :value="$member->religious_name ?? ''"
                                    type="text"
                                    livewireMethod="updateMember"
                                    placeholder="Add religious name..."
                                />
                            </td>
                            <td class="py-4 px-6">
                                <x-forms.inline-edit
                                    :id="$member->id"
                                    field="status"
                                    :value="$member->status->value"
                                    type="select"
                                    :options="collect(\App\Enums\MemberStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->name])->toArray()"
                                    livewireMethod="updateMember"
                                    displayClass="px-2 py-1 text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800"
                                />
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
                </x-slot>

                <x-slot name="mobileContent">
                    @forelse($members as $member)
                        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-3">
                                    <input wire:model.live="selected" value="{{ $member->id }}" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</h3>
                                        @if($member->religious_name)
                                            <p class="text-sm text-gray-500">{{ $member->religious_name }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    {{ $member->status->name }}
                                </span>
                            </div>
                            
                            <div class="flex justify-end gap-3 mt-4 pt-3 border-t border-gray-100">
                                <a href="{{ route('members.show', $member) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">View Details</a>
                                <a href="{{ route('members.edit', $member) }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">Edit</a>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-4 rounded-lg shadow text-center text-gray-500">
                            No members found.
                        </div>
                    @endforelse
                </x-slot>
            </x-tables.responsive-table>

            <!-- Smart Pagination -->
            <x-tables.smart-pagination
                :paginator="$members"
                :showPageSize="true"
                :showJumpTo="true"
                :showInfiniteScroll="false"
                :pageSizeOptions="[10, 25, 50, 100]"
                wire:key="pagination-{{ $members->currentPage() }}"
            />
        </div>
    </div>
</div>
