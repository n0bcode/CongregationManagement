@props(['disabled' => false])

@php
$hasError = false;
if ($attributes->has('name')) {
    $name = $attributes->get('name');
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $hasError = isset($errors) && $errors->has($errorKey);
    // dump(['name' => $name, 'errorKey' => $errorKey, 'hasError' => $hasError, 'errors' => isset($errors) ? $errors->keys() : 'null']);
}
@endphp

<input @disabled($disabled) {{ $attributes->merge([
    'class' => 'form-input shadow-sm bg-white border-stone-300 text-slate-800 focus:border-amber-500 focus:ring-amber-500 rounded-md',
    'aria-invalid' => $hasError ? 'true' : 'false',
    'aria-describedby' => $hasError && isset($name) ? "{$name}-error" : null,
]) }}>
