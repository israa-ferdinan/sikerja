@props([
    'active' => false,
    'icon' => null,
])

@php
    $classes = ($active ?? false)
        ? 'border-blue-500 bg-blue-50 text-blue-700'
        : 'border-transparent text-slate-600 hover:border-slate-200 hover:bg-slate-50 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => 'flex w-full items-center gap-3 border-l-4 px-3 py-2 text-start text-base font-medium transition ' . $classes]) }}>
    @if($icon)
        <x-icon :name="$icon" class="h-5 w-5" />
    @endif

    <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>
</a>
