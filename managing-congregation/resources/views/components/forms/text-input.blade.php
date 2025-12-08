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
    'class' => 'form-input shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-amber-500 dark:focus:border-amber-600 focus:ring-amber-500 dark:focus:ring-amber-600 rounded-md',
    'aria-invalid' => $hasError ? 'true' : 'false',
    'aria-describedby' => $hasError && isset($name) ? "{$name}-error" : null,
]) }}>
