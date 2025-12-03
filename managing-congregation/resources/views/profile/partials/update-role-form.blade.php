<section>
    <header>
        <h2 class="text-lg font-heading font-semibold text-slate-800">
            🧪 Change Role (Testing Only)
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            This feature is only available in development/testing environments. Use it to test different role permissions.
        </p>
    </header>

    <form method="post" action="{{ route('profile.role.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="role" class="form-label">Select Role</label>
            <select id="role" name="role" class="form-select" required>
                <option value="super_admin" {{ Auth::user()->role->value === 'super_admin' ? 'selected' : '' }}>
                    Super Admin (Full Access)
                </option>
                <option value="general" {{ Auth::user()->role->value === 'general' ? 'selected' : '' }}>
                    General Secretary (All Communities)
                </option>
   
               <option value="director" {{ Auth::user()->role->value === 'director' ? 'selected' : '' }}>
                    Community Director (Own Community Only)
                </option>
                <option value="secretary" {{ Auth::user()->role->value === 'secretary' ? 'selected' : '' }}>
                    Secretary (Data Entry)
                </option>
                <option value="member" {{ Auth::user()->role->value === 'member' ? 'selected' : '' }}>
                    Member (View Own Profile)
                </option>
            </select>

            @error('role')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-sm text-amber-800">
                    <p class="font-semibold">Current Role: {{ Auth::user()->role->value }}</p>
                    <p class="mt-1">Changing your role will affect what you can see and do in the system. This is for testing purposes only.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-button type="submit" variant="primary">
                Change Role
            </x-button>

            @if (session('status') === 'Role updated successfully (Testing Mode)')
                <p class="text-sm text-emerald-600 font-medium">Role changed successfully!</p>
            @endif
        </div>
    </form>
</section>
