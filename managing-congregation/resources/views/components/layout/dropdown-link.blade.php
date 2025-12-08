@props(['active' => false])

@php
$classes = $active
    ? 'block w-full px-4 py-2 text-start text-sm leading-5 font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 focus:outline-none focus:bg-amber-100 transition duration-150 ease-in-out'
    : 'block w-full px-4 py-2 text-start text-sm leading-5 text-slate-700 hover:bg-stone-50 hover:text-amber-600 focus:outline-none focus:bg-stone-100 focus:text-amber-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} role="menuitem">
    {{ $slot }}
</a>
