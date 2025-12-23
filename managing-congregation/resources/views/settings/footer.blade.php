<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Footer Settings') }}
            </h2>
            <a href="{{ route('admin.settings.index') }}" class="text-sm text-amber-600 hover:text-amber-700 transition-colors">
                ← {{ __('Back to Settings') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-stone-600 mb-6">
                        {{ __('Customize the footer content that appears at the bottom of all pages.') }}
                    </p>

                    <form action="{{ route('admin.settings.footer.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6" 
                        x-data="{
                            description: '{{ old('footer_description', $footerSettings['footer_description']->value ?? '') }}',
                            address: '{{ old('footer_address', $footerSettings['footer_address']->value ?? '') }}',
                            email: '{{ old('footer_email', $footerSettings['footer_email']->value ?? '') }}',
                            copyright: '{{ old('footer_copyright', $footerSettings['footer_copyright']->value ?? '') }}'
                        }">
                        @csrf
                        @method('PUT')
                        
                        <!-- Footer Description -->
                        <div>
                            <x-ui.label for="footer_description" :value="__('Footer Description')" />
                            <textarea 
                                id="footer_description" 
                                name="footer_description" 
                                class="mt-1 block w-full border-stone-300 focus:border-amber-500 focus:ring-amber-500 rounded-md shadow-sm" 
                                rows="3"
                                x-model="description"
                                required
                            >{{ old('footer_description', $footerSettings['footer_description']->value ?? '') }}</textarea>
                            <div class="mt-1 flex items-start justify-between gap-2">
                                <p class="text-xs text-stone-500">{{ __('Brief description about your organization (max 500 characters)') }}</p>
                                @if(isset($footerSettings['footer_description']->value))
                                    <p class="text-xs text-amber-600 italic flex-shrink-0">
                                        {{ __('Current') }}: "{{ Str::limit($footerSettings['footer_description']->value, 50) }}"
                                    </p>
                                @endif
                            </div>
                            @error('footer_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Footer Address -->
                        <div>
                            <x-ui.label for="footer_address" :value="__('Contact Address')" />
                            <input 
                                id="footer_address" 
                                name="footer_address" 
                                type="text" 
                                class="mt-1 block w-full border-stone-300 focus:border-amber-500 focus:ring-amber-500 rounded-md shadow-sm" 
                                x-model="address"
                                required 
                            />
                            <div class="mt-1 flex items-center justify-between gap-2">
                                <p class="text-xs text-stone-500">{{ __('Physical address of your organization') }}</p>
                                @if(isset($footerSettings['footer_address']->value))
                                    <p class="text-xs text-amber-600 italic flex-shrink-0">
                                        {{ __('Current') }}: {{ $footerSettings['footer_address']->value }}
                                    </p>
                                @endif
                            </div>
                            @error('footer_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Footer Email -->
                        <div>
                            <x-ui.label for="footer_email" :value="__('Contact Email')" />
                            <input 
                                id="footer_email" 
                                name="footer_email" 
                                type="email" 
                                class="mt-1 block w-full border-stone-300 focus:border-amber-500 focus:ring-amber-500 rounded-md shadow-sm" 
                                x-model="email"
                                required 
                            />
                            <div class="mt-1 flex items-center justify-between gap-2">
                                <p class="text-xs text-stone-500">{{ __('Public email address for inquiries') }}</p>
                                @if(isset($footerSettings['footer_email']->value))
                                    <p class="text-xs text-amber-600 italic flex-shrink-0">
                                        {{ __('Current') }}: {{ $footerSettings['footer_email']->value }}
                                    </p>
                                @endif
                            </div>
                            @error('footer_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Footer Copyright -->
                        <div>
                            <x-ui.label for="footer_copyright" :value="__('Copyright Text')" />
                            <input 
                                id="footer_copyright" 
                                name="footer_copyright" 
                                type="text" 
                                class="mt-1 block w-full border-stone-300 focus:border-amber-500 focus:ring-amber-500 rounded-md shadow-sm" 
                                x-model="copyright"
                                required 
                            />
                            <div class="mt-1 flex items-center justify-between gap-2">
                                <p class="text-xs text-stone-500">{{ __('Copyright notice (you can use HTML entities like &copy;)') }}</p>
                                @if(isset($footerSettings['footer_copyright']->value))
                                    <p class="text-xs text-amber-600 italic flex-shrink-0">
                                        {{ __('Current') }}: {{ Str::limit(strip_tags($footerSettings['footer_copyright']->value), 40) }}
                                    </p>
                                @endif
                            </div>
                            @error('footer_copyright')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Footer Logo -->
                        <div x-data="{ 
                            logoPreview: '{{ \App\Models\SystemSetting::get('footer_logo_path') ? asset('storage/' . \App\Models\SystemSetting::get('footer_logo_path')) : '' }}',
                            logoFile: null,
                            updatePreview(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    this.logoFile = file;
                                    const reader = new FileReader();
                                    reader.onload = (e) => this.logoPreview = e.target.result;
                                    reader.readAsDataURL(file);
                                }
                            },
                            removeLogo() {
                                this.logoPreview = '';
                                this.logoFile = null;
                                document.getElementById('footer_logo').value = '';
                            }
                        }">
                            <x-ui.label for="footer_logo" :value="__('Footer Logo (Optional)')" />
                            <div class="mt-1">
                                <!-- Current Logo Preview -->
                                <div x-show="logoPreview" class="mb-3">
                                    <img :src="logoPreview" alt="Logo Preview" class="h-16 w-auto object-contain border border-stone-200 rounded-md p-2 bg-white">
                                    <button type="button" @click="removeLogo()" class="mt-2 text-xs text-red-600 hover:text-red-700">
                                        {{ __('Remove Logo') }}
                                    </button>
                                </div>
                                
                                <!-- File Input -->
                                <input 
                                    id="footer_logo" 
                                    name="footer_logo" 
                                    type="file" 
                                    accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml"
                                    @change="updatePreview($event)"
                                    class="block w-full text-sm text-stone-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-amber-50 file:text-amber-700
                                        hover:file:bg-amber-100
                                        cursor-pointer"
                                />
                                <input type="hidden" name="remove_logo" x-model="logoPreview === '' ? '1' : '0'">
                            </div>
                            <p class="mt-1 text-xs text-stone-500">
                                {{ __('Upload a custom logo (PNG, JPG, WebP, SVG, or ICO, max 2MB)') }}<br>
                                <span class="text-amber-600">{{ __('Images will be automatically optimized to WebP format and resized to 512px for best performance.') }}</span>
                            </p>
                            @error('footer_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-stone-800 mb-4">{{ __('Live Preview') }}</h3>
                            <p class="text-xs text-stone-500 mb-4">{{ __('Preview updates automatically as you type') }}</p>
                            <div class="bg-stone-50 border border-stone-200 rounded-lg p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                                    <!-- Brand & Contact Preview -->
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-2">
                                            <x-application-logo class="block h-6 w-auto fill-current text-amber-600" />
                                            <span class="text-lg font-serif font-bold text-stone-800">{{ config('app.name') }}</span>
                                        </div>
                                        <p class="text-stone-500 text-sm leading-relaxed" x-text="description"></p>
                                        <div class="flex flex-col gap-1 text-sm text-stone-500">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span x-text="address"></span>
                                            </span>
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <span x-text="email"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-span-2"></div>
                                </div>
                                <div class="mt-6 border-t border-stone-200 pt-4">
                                    <p class="text-stone-400 text-sm" x-html="copyright"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <a href="{{ route('admin.settings.index') }}" class="text-sm text-stone-600 hover:text-stone-700 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <x-ui.primary-button>
                                {{ __('Save Footer Settings') }}
                            </x-ui.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
