<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('User Management') }}">
            <x-slot:actions>
                <a href="{{ route('admin.permissions.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-stone-300 rounded-lg text-slate-700 hover:bg-stone-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    {{ __('Permissions') }}
                </a>
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search and Filter --}}
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Search') }}
                    </label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="{{ __('Search by name or email...') }}"
                           class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <div class="md:w-64">
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Filter by Role') }}
                    </label>
                    <select name="role" 
                            id="role"
                            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="">{{ __('All Roles') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" {{ request('role') === $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="px-6 py-2 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500 transition-colors">
                        {{ __('Search') }}
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-6 py-2 bg-white border border-stone-300 text-slate-700 font-medium rounded-lg hover:bg-stone-50 focus:outline-none focus:ring-4 focus:ring-stone-300 transition-colors">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead class="bg-stone-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                {{ __('User') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                {{ __('Email') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                {{ __('Role') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">
                                {{ __('Community') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-700 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-stone-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center">
                                                <span class="text-amber-600 font-medium text-sm">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-slate-900">
                                                {{ $user->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-700">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($user->role === App\Enums\UserRole::SUPER_ADMIN) bg-purple-100 text-purple-800
                                        @elseif($user->role === App\Enums\UserRole::GENERAL) bg-blue-100 text-blue-800
                                        @elseif($user->role === App\Enums\UserRole::DIRECTOR) bg-green-100 text-green-800
                                        @elseif($user->role === App\Enums\UserRole::TREASURER) bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $user->role?->label() ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-700">
                                        {{ $user->community?->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($user->id !== Auth::id())
                                        <button type="button" 
                                                onclick="openRoleModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->role?->value }}', {{ $user->community_id ?? 'null' }})"
                                                class="text-amber-600 hover:text-amber-900">
                                            {{ __('Change Role') }}
                                        </button>
                                    @else
                                        <span class="text-slate-400">{{ __('(You)') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    {{ __('No users found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-stone-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Role Change Modal --}}
    <div id="role-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold text-slate-800 mb-4">
                {{ __('Change User Role') }}
            </h3>

            <form id="role-form" method="POST" action="">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <p class="text-sm text-slate-600 mb-4">
                        {{ __('Changing role for:') }} <strong id="modal-user-name"></strong>
                    </p>
                </div>

                <div class="mb-4">
                    <label for="modal-role" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('New Role') }}
                    </label>
                    <select name="role" 
                            id="modal-role"
                            required
                            onchange="toggleCommunityField()"
                            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="community-field" class="mb-4 hidden">
                    <label for="modal-community" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Community') }} <span class="text-rose-600">*</span>
                    </label>
                    <select name="community_id" 
                            id="modal-community"
                            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="">{{ __('Select Community') }}</option>
                        @foreach ($communities as $community)
                            <option value="{{ $community->id }}">{{ $community->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-4 mt-6">
                    <button type="submit" 
                            class="flex-1 px-6 py-2 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500 transition-colors">
                        {{ __('Update Role') }}
                    </button>
                    <button type="button" 
                            onclick="closeRoleModal()"
                            class="flex-1 px-6 py-2 bg-white border border-stone-300 text-slate-700 font-medium rounded-lg hover:bg-stone-50 focus:outline-none focus:ring-4 focus:ring-stone-300 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openRoleModal(userId, userName, currentRole, currentCommunityId) {
            const modal = document.getElementById('role-modal');
            const form = document.getElementById('role-form');
            const modalUserName = document.getElementById('modal-user-name');
            const modalRole = document.getElementById('modal-role');
            const modalCommunity = document.getElementById('modal-community');

            // Set form action
            form.action = `/admin/users/${userId}/role`;

            // Set user name
            modalUserName.textContent = userName;

            // Set current role
            modalRole.value = currentRole || '';

            // Set current community
            if (currentCommunityId) {
                modalCommunity.value = currentCommunityId;
            } else {
                modalCommunity.value = '';
            }

            // Show/hide community field based on role
            toggleCommunityField();

            // Show modal
            modal.classList.remove('hidden');
        }

        function closeRoleModal() {
            const modal = document.getElementById('role-modal');
            modal.classList.add('hidden');
        }

        function toggleCommunityField() {
            const roleSelect = document.getElementById('modal-role');
            const communityField = document.getElementById('community-field');
            const communitySelect = document.getElementById('modal-community');

            if (roleSelect.value === 'director') {
                communityField.classList.remove('hidden');
                communitySelect.required = true;
            } else {
                communityField.classList.add('hidden');
                communitySelect.required = false;
            }
        }

        // Close modal when clicking outside
        document.getElementById('role-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRoleModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
