<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'general' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'general'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'general' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                General
                            </button>
                            <button @click="activeTab = 'reminders'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'reminders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'reminders' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Reminders
                            </button>
                            <button @click="activeTab = 'email'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'email', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'email' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Email
                            </button>
                            <button @click="activeTab = 'backup'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'backup', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'backup' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Backup
                            </button>
                        </nav>
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        
                        <!-- General Settings -->
                        <div x-show="activeTab === 'general'" class="space-y-6">
                            <div>
                                <x-forms.input-label for="service_year_start" :value="__('Service Year Start Month')" />
                                <select id="service_year_start" name="settings[service_year_start][value]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach(range(1, 12) as $month)
                                        <option value="{{ $month }}" {{ \App\Models\SystemSetting::get('service_year_start', 1) == $month ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="settings[service_year_start][key]" value="service_year_start">
                            </div>
                        </div>

                        <!-- Reminders Settings -->
                        <div x-show="activeTab === 'reminders'" class="space-y-6">
                            <div>
                                <x-forms.input-label for="reminder_vow_expiration" :value="__('Vow Expiration Reminder (Days)')" />
                                <x-forms.text-input id="reminder_vow_expiration" name="settings[reminder_vow_expiration][value]" type="number" class="mt-1 block w-full" :value="\App\Models\SystemSetting::get('reminder_vow_expiration', 30)" />
                                <input type="hidden" name="settings[reminder_vow_expiration][key]" value="reminder_vow_expiration">
                            </div>
                            <div>
                                <x-forms.input-label for="reminder_birthday" :value="__('Birthday Reminder (Days)')" />
                                <x-forms.text-input id="reminder_birthday" name="settings[reminder_birthday][value]" type="number" class="mt-1 block w-full" :value="\App\Models\SystemSetting::get('reminder_birthday', 7)" />
                                <input type="hidden" name="settings[reminder_birthday][key]" value="reminder_birthday">
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div x-show="activeTab === 'email'" class="space-y-6">
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Email settings are currently configured via .env file. Use this section to test connectivity.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4">
                                <h4 class="text-md font-medium text-gray-900">Test Email Configuration</h4>
                                <div class="mt-4 flex gap-4">
                                    <x-forms.text-input id="test_email" name="test_email" type="email" class="block w-full" placeholder="Enter email address" />
                                    <button type="button" onclick="document.getElementById('test-email-form').submit()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                        Test Connection
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Backup Settings -->
                        <div x-show="activeTab === 'backup'" class="space-y-6">
                             <div>
                                <x-forms.input-label for="backup_enabled" :value="__('Enable Daily Backups')" />
                                <select id="backup_enabled" name="settings[backup_enabled][value]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="1" {{ \App\Models\SystemSetting::get('backup_enabled', false) ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !\App\Models\SystemSetting::get('backup_enabled', false) ? 'selected' : '' }}>No</option>
                                </select>
                                <input type="hidden" name="settings[backup_enabled][key]" value="backup_enabled">
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <x-ui.primary-button>
                                {{ __('Save Settings') }}
                            </x-ui.primary-button>
                        </div>
                    </form>

                    <form id="test-email-form" action="{{ route('admin.settings.test-email') }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="email" id="hidden_test_email">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('test_email').addEventListener('input', function(e) {
            document.getElementById('hidden_test_email').value = e.target.value;
        });
    </script>
</x-app-layout>
