@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full text-white bg-blue-800 rounded-lg px-4 py-2 text-base font-medium transition-colors duration-150 ease-in-out'
            : 'flex items-center w-full text-white hover:bg-blue-600 rounded-lg px-4 py-2 text-base font-medium transition-colors duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
