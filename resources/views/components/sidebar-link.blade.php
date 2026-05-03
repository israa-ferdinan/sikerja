@props([
    'active' => false,
    'href' => '#',
])

@php
    $classes = $active
        ? 'bg-blue-600 text-white border-blue-800 shadow-sm'
        : 'text-gray-700 border-transparent hover:bg-gray-100 hover:text-gray-900';

    $baseClasses = 'group flex items-center rounded-lg border-l-4 px-3 py-2 text-sm font-semibold transition';
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => $baseClasses . ' ' . $classes]) }}>
    {{ $slot }}
</a>