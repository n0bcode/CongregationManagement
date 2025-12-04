<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-slate-800">
                {{ __('Permission Management') }}
            </h2>
            <a href="{{ route('admin.permissions.audit') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-stone-300 rounded-lg text-slate-700 hover:bg-stone-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('View Audit Log') }}
            </a>
        </div>
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

        {{-- Role Selector --}}
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8 mb-6">
            <label for="role-selector" class="block text-lg font-medium text-slate-700 mb-3">
                {{ __('Select Role to Manage') }}
            </label>
            <select id="role-selector" 
                    class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none">
                <option value="">{{ __('-- Select a Role --') }}</option>
                @foreach ($roles as $role)
                    @if ($role !== App\Enums\UserRole::SUPER_ADMIN)
                        <option value="{{ $role->value }}">{{ $role->name }}</option>
                    @endif
                @endforeach
            </select>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('Note: Super Admin has universal access and does not require explicit permissions.') }}
            </p>
        </div>

        {{-- Permission Matrix --}}
        <div id="permission-matrix" class="hidden">
            <form id="permission-form" method="POST" action="{{ route('admin.permissions.update') }}">
                @csrf
                <input type="hidden" name="role" id="selected-role">

                <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
                    <h3 class="text-xl font-semibold text-slate-800 mb-6">
                        {{ __('Permissions for') }} <span id="role-name" class="text-amber-600"></span>
                    </h3>

                    {{-- Permissions by Module --}}
                    <div class="space-y-8">
                        @foreach ($permissions as $module => $modulePermissions)
                            <div class="border-b border-stone-200 pb-6 last:border-b-0">
                                <h4 class="text-lg font-medium text-slate-700 mb-4 capitalize">
                                    {{ __($module) }} {{ __('Module') }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($modulePermissions as $permission)
                                        <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-stone-50 cursor-pointer transition-colors">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->key }}"
                                                   data-permission="{{ $permission->key }}"
                                                   class="w-5 h-5 text-amber-600 border-stone-300 rounded focus:ring-amber-500 focus:ring-2">
                                            <span class="text-base text-slate-700">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-4 pt-8 mt-8 border-t border-stone-200">
                        <button type="submit" 
                                class="flex-1 min-h-[48px] px-6 py-3 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-500 transition-colors">
                            {{ __('Save Permissions') }}
                        </button>
                        <button type="button" 
                                id="cancel-btn"
                                class="flex-1 min-h-[48px] px-6 py-3 bg-white border border-stone-300 text-slate-700 font-medium rounded-lg hover:bg-stone-50 focus:outline-none focus:ring-4 focus:ring-stone-300 transition-colors">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Loading State --}}
        <div id="loading-state" class="hidden text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-amber-600"></div>
            <p class="mt-4 text-slate-600">{{ __('Loading permissions...') }}</p>
        </div>
    </div>

    @push('scripts')
    <script>
        const rolePermissions = @json($rolePermissions);
        const roleSelector = document.getElementById('role-selector');
        const permissionMatrix = document.getElementById('permission-matrix');
        const loadingState = document.getElementById('loading-state');
        const selectedRoleInput = document.getElementById('selected-role');
        const roleNameSpan = document.getElementById('role-name');
        const cancelBtn = document.getElementById('cancel-btn');

        roleSelector.addEventListener('change', function() {
            const selectedRole = this.value;
            
            if (!selectedRole) {
                permissionMatrix.classList.add('hidden');
                return;
            }

            // Show loading
            loadingState.classList.remove('hidden');
            permissionMatrix.classList.add('hidden');

            // Simulate loading (in real app, this would be AJAX)
            setTimeout(() => {
                // Update form
                selectedRoleInput.value = selectedRole;
                roleNameSpan.textContent = this.options[this.selectedIndex].text;

                // Uncheck all checkboxes first
                document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Check permissions for selected role
                const permissions = rolePermissions[selectedRole] || [];
                permissions.forEach(permissionKey => {
                    const checkbox = document.querySelector(`input[data-permission="${permissionKey}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });

                // Show matrix
                loadingState.classList.add('hidden');
                permissionMatrix.classList.remove('hidden');
            }, 300);
        });

        cancelBtn.addEventListener('click', function() {
            roleSelector.value = '';
            permissionMatrix.classList.add('hidden');
        });
    </script>
    @endpush
</x-app-layout>
