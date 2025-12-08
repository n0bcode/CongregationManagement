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
                    <x-enhanced-search
                        model="search"
                        placeholder="Search members by name, status, community..."
                        :suggestions="[
                            'Active members',
                            'In Formation',
                            'Novitiate',
                            'Professed',
                            'Teaching',
                            'Healthcare'
                        ]"
                        class="flex-grow max-w-md"
                    />

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

                <!-- Bulk Actions Menu -->
                <x-bulk-actions-menu
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
                                    <x-inline-edit
                                        :id="$member->id"
                                        field="religious_name"
                                        :value="$member->religious_name ?? ''"
                                        type="text"
                                        livewireMethod="updateMember"
                                        placeholder="Add religious name..."
                                    />
                                </td>
                                <td class="py-4 px-6">
                                    <x-inline-edit
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
                    </tbody>
                </table>
            </div>

            <!-- Smart Pagination -->
            <x-smart-pagination
                :paginator="$members"
                :showPageSize="true"
                :showJumpTo="true"
                :showInfiniteScroll="false"
                :pageSizeOptions="[10, 25, 50, 100]"
            />
        </div>
    </div>
</div>
