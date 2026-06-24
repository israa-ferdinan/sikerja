@props([
    'active' => false,
    'href' => '#',
    'icon' => null,
])

@php
    $classes = $active
        ? 'border-blue-600 bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-100'
        : 'border-transparent text-slate-600 hover:border-slate-200 hover:bg-slate-100 hover:text-slate-900';

    $iconClasses = $active
        ? 'text-blue-600'
        : 'text-slate-400 group-hover:text-slate-600';

    $baseClasses = 'group flex items-center gap-3 rounded-xl border px-3 py-2.5 text-sm font-semibold transition';
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => $baseClasses . ' ' . $classes]) }}>
    @if($icon)
        <x-icon :name="$icon" class="h-5 w-5 {{ $iconClasses }}" />
    @endif

    <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>
</a>
