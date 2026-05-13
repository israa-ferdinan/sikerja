@props([
    'variant' => 'neutral',
])

@php
    $classes = [
        'neutral' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'primary' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'warning' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'danger' => 'bg-red-50 text-red-700 ring-red-200',
        'info' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
    ];

    $class = $classes[$variant] ?? $classes['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {$class}"]) }}>
    {{ $slot }}
</span>