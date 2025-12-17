<nav x-data="{ open: false }" class="bg-white border-b border-stone-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.webp') }}" alt="{{ config('app.name', 'Logo') }}"
                            class="w-10 h-10 rounded-full object-cover" />
                        <span class="font-serif text-xl text-slate-800 hidden sm:block">Congregation</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    {{-- Dashboard - standalone --}}
                    <x-layout.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="font-medium">
                        {{ __('Dashboard') }}
                    </x-layout.nav-link>

                    {{-- Management Dropdown --}}
                    <x-layout.nav-dropdown label="{{ __('Management') }}" :active="request()->routeIs('members.*', 'documents.*', 'celebrations.*', 'communities.*', 'projects.*', 'periodic-events.*')">
                        <x-layout.dropdown-link :href="route('members.index')"
                            :active="request()->routeIs('members.*')">
                            {{ __('Members') }}
                        </x-layout.dropdown-link>
                        @can('viewAny', \App\Models\Community::class)
                            <x-layout.dropdown-link :href="route('communities.index')"
                                :active="request()->routeIs('communities.*')">
                                {{ __('Communities') }}
                            </x-layout.dropdown-link>
                        @endcan
                        @can('viewAny', \App\Models\Document::class)
                            <x-layout.dropdown-link :href="route('documents.index')"
                                :active="request()->routeIs('documents.*')">
                                {{ __('Documents') }}
                            </x-layout.dropdown-link>
                        @endcan
                        <x-layout.dropdown-link :href="route('celebrations.index')"
                            :active="request()->routeIs('celebrations.*')">
                            {{ __('Celebrations') }}
                        </x-layout.dropdown-link>
                        <x-layout.dropdown-link :href="route('projects.index')"
                            :active="request()->routeIs('projects.*')">
                            {{ __('Projects') }}
                        </x-layout.dropdown-link>
                        <x-layout.dropdown-link :href="route('periodic-events.index')"
                            :active="request()->routeIs('periodic-events.*')">
                            {{ __('Periodic Events') }}
                        </x-layout.dropdown-link>
                    </x-layout.nav-dropdown>

                    {{-- System Dropdown (admin only) --}}
                    @can('viewAny', \App\Models\AuditLog::class)
                        <x-layout.nav-dropdown label="{{ __('System') }}" :active="request()->routeIs('audit-logs.*', 'admin.users.*', 'admin.permissions.*', 'admin.settings.*', 'admin.backups.*')">
                            <x-layout.dropdown-link :href="route('audit-logs.index')"
                                :active="request()->routeIs('audit-logs.*')">
                                {{ __('Audit Logs') }}
                            </x-layout.dropdown-link>
                            @can('view-admin')
                                <x-layout.dropdown-link :href="route('admin.users.index')"
                                    :active="request()->routeIs('admin.users.*')">
                                    {{ __('Users') }}
                                </x-layout.dropdown-link>

                                <x-layout.dropdown-link :href="route('admin.permissions.index')"
                                    :active="request()->routeIs('admin.permissions.*')">
                                    {{ __('Permissions') }}
                                </x-layout.dropdown-link>

                                <x-layout.dropdown-link :href="route('admin.settings.index')"
                                    :active="request()->routeIs('admin.settings.*')">
                                    {{ __('Settings') }}
                                </x-layout.dropdown-link>
                                <x-layout.dropdown-link :href="route('admin.backups.index')"
                                    :active="request()->routeIs('admin.backups.*')">
                                    {{ __('Backups') }}
                                </x-layout.dropdown-link>
                            @endcan
                        </x-layout.nav-dropdown>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-layout.dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-4 py-2 border border-stone-200 text-sm leading-4 font-medium rounded-lg text-slate-700 bg-white hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition ease-in-out duration-150 shadow-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-slate-200 rounded-full flex items-center justify-center">
                                    <span
                                        class="text-xs font-medium text-slate-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-stone-200">
                            <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>

                        <x-layout.dropdown-link :href="route('projects.index')">
                            {{ __('My Projects') }}
                        </x-layout.dropdown-link>
                        <x-layout.dropdown-link :href="route('my-tasks.index')">
                            {{ __('My Tasks') }}
                        </x-layout.dropdown-link>
                        <x-layout.dropdown-link :href="route('notifications.index')">
                            {{ __('Notifications') }}
                        </x-layout.dropdown-link>

                        <div class="border-t border-gray-100"></div>

                        <x-layout.dropdown-link :href="route('profile.edit')">
                            {{ __('Profile Settings') }}
                        </x-layout.dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-layout.dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-layout.dropdown-link>
                        </form>
                    </x-slot>
                </x-layout.dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-stone-100 focus:outline-none focus:bg-stone-100 focus:text-slate-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-stone-50 border-t border-stone-200">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Dashboard - standalone --}}
            <x-layout.responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="font-medium">
                {{ __('Dashboard') }}
            </x-layout.responsive-nav-link>

            {{-- Management Section --}}
            <div
                x-data="{ expanded: {{ request()->routeIs('members.*', 'documents.*', 'celebrations.*', 'projects.*', 'periodic-events.*') ? 'true' : 'false' }} }">
                <button @click="expanded = !expanded"
                    class="w-full flex items-center justify-between px-4 py-2 text-base font-medium text-slate-700 hover:bg-stone-100 transition duration-150 ease-in-out"
                    :class="{'text-amber-600 bg-amber-50': {{ request()->routeIs('members.*', 'documents.*', 'celebrations.*', 'projects.*', 'periodic-events.*') ? 'true' : 'false' }} }">
                    <span>{{ __('Management') }}</span>
                    <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" x-collapse class="bg-white">
                    <x-layout.responsive-nav-link :href="route('members.index')"
                        :active="request()->routeIs('members.*')" class="pl-8">
                        {{ __('Members') }}
                    </x-layout.responsive-nav-link>
                    @can('viewAny', \App\Models\Document::class)
                        <x-layout.responsive-nav-link :href="route('documents.index')"
                            :active="request()->routeIs('documents.*')" class="pl-8">
                            {{ __('Documents') }}
                        </x-layout.responsive-nav-link>
                    @endcan
                    <x-layout.responsive-nav-link :href="route('celebrations.index')"
                        :active="request()->routeIs('celebrations.*')" class="pl-8">
                        {{ __('Celebrations') }}
                    </x-layout.responsive-nav-link>
                    <x-layout.responsive-nav-link :href="route('projects.index')"
                        :active="request()->routeIs('projects.*')" class="pl-8">
                        {{ __('Projects') }}
                    </x-layout.responsive-nav-link>
                    <x-layout.responsive-nav-link :href="route('periodic-events.index')"
                        :active="request()->routeIs('periodic-events.*')" class="pl-8">
                        {{ __('Periodic Events') }}
                    </x-layout.responsive-nav-link>
                </div>
            </div>

            {{-- System Section (admin only) --}}
            @can('viewAny', \App\Models\AuditLog::class)
                <div
                    x-data="{ expanded: {{ request()->routeIs('audit-logs.*', 'admin.users.*', 'admin.permissions.*', 'admin.settings.*', 'admin.backups.*') ? 'true' : 'false' }} }">
                    <button @click="expanded = !expanded"
                        class="w-full flex items-center justify-between px-4 py-2 text-base font-medium text-slate-700 hover:bg-stone-100 transition duration-150 ease-in-out"
                        :class="{'text-amber-600 bg-amber-50': {{ request()->routeIs('audit-logs.*', 'admin.users.*', 'admin.permissions.*', 'admin.settings.*', 'admin.backups.*') ? 'true' : 'false' }} }">
                        <span>{{ __('System') }}</span>
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="expanded" x-collapse class="bg-white">
                        <x-layout.responsive-nav-link :href="route('audit-logs.index')"
                            :active="request()->routeIs('audit-logs.*')" class="pl-8">
                            {{ __('Audit Logs') }}
                        </x-layout.responsive-nav-link>
                        @can('view-admin')
                            <x-layout.responsive-nav-link :href="route('admin.users.index')"
                                :active="request()->routeIs('admin.users.*')" class="pl-8">
                                {{ __('Users') }}
                            </x-layout.responsive-nav-link>

                            <x-layout.responsive-nav-link :href="route('admin.permissions.index')"
                                :active="request()->routeIs('admin.permissions.*')" class="pl-8">
                                {{ __('Permissions') }}
                            </x-layout.responsive-nav-link>

                            <x-layout.responsive-nav-link :href="route('admin.settings.index')"
                                :active="request()->routeIs('admin.settings.*')" class="pl-8">
                                {{ __('Settings') }}
                            </x-layout.responsive-nav-link>
                            <x-layout.responsive-nav-link :href="route('admin.backups.index')"
                                :active="request()->routeIs('admin.backups.*')" class="pl-8">
                                {{ __('Backups') }}
                            </x-layout.responsive-nav-link>
                        @endcan
                    </div>
                </div>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-stone-200">
            <div class="px-4 py-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-slate-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-layout.responsive-nav-link :href="route('projects.index')">
                    {{ __('My Projects') }}
                </x-layout.responsive-nav-link>
                <x-layout.responsive-nav-link :href="route('my-tasks.index')">
                    {{ __('My Tasks') }}
                </x-layout.responsive-nav-link>
                <x-layout.responsive-nav-link :href="route('notifications.index')">
                    {{ __('Notifications') }}
                </x-layout.responsive-nav-link>

                <x-layout.responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile Settings') }}
                </x-layout.responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-layout.responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-layout.responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>