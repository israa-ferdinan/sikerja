@props([
    'padding' => 'p-5',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm']) }}>
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>