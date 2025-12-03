@props(['active' => false])

@php
$classes = $active
    ? 'block w-full px-4 py-2 text-start text-sm leading-5 text-slate-900 bg-stone-50 focus:outline-none focus:bg-stone-100 transition duration-150 ease-in-out'
    : 'block w-full px-4 py-2 text-start text-sm leading-5 text-slate-700 hover:bg-stone-50 focus:outline-none focus:bg-stone-100 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
