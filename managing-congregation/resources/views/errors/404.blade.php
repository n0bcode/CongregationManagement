<x-guest-layout>
    <div class="text-center">
        <h1 class="text-6xl font-bold text-stone-800 dark:text-stone-200 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-stone-600 dark:text-stone-400 mb-6">Page Not Found</h2>
        <p class="text-stone-500 dark:text-stone-400 mb-8">
            {{ __($exception->getMessage() ?: 'Sorry, the page you are looking for could not be found.') }}
        </p>
        <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-stone-800 dark:bg-stone-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-stone-800 uppercase tracking-widest hover:bg-stone-700 dark:hover:bg-white focus:bg-stone-700 dark:focus:bg-white active:bg-stone-900 dark:active:bg-stone-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-stone-800 transition ease-in-out duration-150">
            {{ __('Return Home') }}
        </a>
    </div>
</x-guest-layout>
