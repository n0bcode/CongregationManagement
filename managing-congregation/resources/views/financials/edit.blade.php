<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('financials.show', $expense) }}" class="mr-4 text-slate-600 hover:text-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="text-3xl font-bold text-stone-800">
                {{ __('Edit Expense') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
            <form method="POST" action="{{ route('financials.update', $expense) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Community --}}
                <div>
                    <label for="community_id" class="block text-lg font-medium text-slate-800 mb-2">
                        {{ __('Community') }} <span class="text-rose-600">*</span>
                    </label>
                    <select 
                        name="community_id" 
                        id="community_id"
                        required
                        class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('community_id') border-rose-500 @enderror"
                    >
                        <option value="">{{ __('Select a community') }}</option>
                        @foreach($communities as $community)
                            <option value="{{ $community->id }}" {{ old('community_id', $expense->community_id) == $community->id ? 'selected' : '' }}>
                                {{ $community->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('community_id')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date and Category --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="date" class="block text-lg font-medium text-slate-800 mb-2">
                            {{ __('Date') }} <span class="text-rose-600">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="date" 
                            id="date"
                            value="{{ old('date', $expense->date->format('Y-m-d')) }}"
                            required
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('date') border-rose-500 @enderror"
                        >
                        @error('date')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-lg font-medium text-slate-800 mb-2">
                            {{ __('Category') }} <span class="text-rose-600">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="category" 
                            id="category"
                            value="{{ old('category', $expense->category) }}"
                            required
                            placeholder="{{ __('e.g., Utilities, Groceries') }}"
                            list="category-suggestions"
                            class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('category') border-rose-500 @enderror"
                        >
                        <datalist id="category-suggestions">
                            <option value="Utilities">
                            <option value="Groceries">
                            <option value="Maintenance">
                            <option value="Supplies">
                            <option value="Transportation">
                            <option value="Healthcare">
                            <option value="Education">
                            <option value="Ministry">
                            <option value="Other">
                        </datalist>
                        @error('category')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Amount --}}
                <div>
                    <label for="amount" class="block text-lg font-medium text-slate-800 mb-2">
                        {{ __('Amount') }} <span class="text-rose-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-lg">$</span>
                        <input 
                            type="number" 
                            name="amount" 
                            id="amount"
                            value="{{ old('amount', number_format($expense->amount / 100, 2, '.', '')) }}"
                            step="0.01"
                            min="0.01"
                            required
                            placeholder="0.00"
                            class="w-full min-h-[48px] pl-8 pr-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('amount') border-rose-500 @enderror"
                        >
                    </div>
                    @error('amount')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-lg font-medium text-slate-800 mb-2">
                        {{ __('Description') }} <span class="text-rose-600">*</span>
                    </label>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="4"
                        required
                        placeholder="{{ __('Provide details about this expense...') }}"
                        class="w-full px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg placeholder:text-slate-400 focus:border-amber-600 focus:ring-4 focus:ring-amber-500 focus:outline-none @error('description') border-rose-500 @enderror"
                    >{{ old('description', $expense->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Current Receipt --}}
                @if($expense->receipt_path)
                    <div>
                        <label class="block text-lg font-medium text-slate-800 mb-2">
                            {{ __('Current Receipt') }}
                        </label>
                        <a 
                            href="{{ asset('storage/' . $expense->receipt_path) }}" 
                            target="_blank"
                            class="inline-flex items-center px-4 py-3 bg-stone-100 hover:bg-stone-200 text-slate-800 rounded-lg transition-colors min-h-[48px]"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            {{ __('View Current Receipt') }}
                        </a>
                    </div>
                @endif

                {{-- Receipt Upload --}}
                <div>
                    <label for="receipt" class="block text-lg font-medium text-slate-800 mb-2">
                        {{ $expense->receipt_path ? __('Replace Receipt') : __('Receipt') }}
                        <span class="text-slate-500 text-sm font-normal">({{ __('Optional') }})</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            type="file" 
                            name="receipt" 
                            id="receipt"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-base text-slate-600
                                file:mr-4 file:py-3 file:px-6
                                file:rounded-lg file:border-0
                                file:text-base file:font-medium
                                file:bg-amber-50 file:text-amber-700
                                file:min-h-[48px]
                                hover:file:bg-amber-100
                                file:cursor-pointer
                                focus:outline-none focus:ring-4 focus:ring-amber-500
                                @error('receipt') border-rose-500 @enderror"
                        >
                    </div>
                    <p class="mt-2 text-sm text-slate-500">
                        {{ __('PDF, JPG, or PNG. Max 10MB.') }}
                        @if($expense->receipt_path)
                            {{ __('Uploading a new file will replace the existing receipt.') }}
                        @endif
                    </p>
                    @error('receipt')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form Actions --}}
                <div class="flex gap-4 pt-6 border-t border-stone-200">
                    <x-button type="submit" variant="primary" class="flex-1">
                        {{ __('Update Expense') }}
                    </x-button>
                    <x-button variant="secondary" href="{{ route('financials.show', $expense) }}" class="flex-1">
                        {{ __('Cancel') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
