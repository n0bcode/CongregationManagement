@php
    $logoPath = \App\Models\SystemSetting::get('footer_logo_path');
@endphp

@if($logoPath && file_exists(storage_path('app/public/' . $logoPath)))
    <img {{ $attributes }} src="{{ asset('storage/' . $logoPath) }}" alt="{{ config('app.name', 'Logo') }}" />
@else
    <img {{ $attributes }} src="{{ asset('images/logo.webp') }}" alt="{{ config('app.name', 'Logo') }}" />
@endif