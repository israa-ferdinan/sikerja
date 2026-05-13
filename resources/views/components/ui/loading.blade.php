@props([
    'target' => null,
    'text' => 'Memuat data...',
])

<div
    @if ($target)
        wire:loading.flex
        wire:target="{{ $target }}"
    @else
        wire:loading.flex
    @endif
    {{ $attributes->merge(['class' => 'hidden items-center gap-2 text-sm text-slate-500']) }}
>
    <span class="h-4 w-4 animate-spin rounded-full border-2 border-slate-400 border-t-transparent"></span>
    <span>{{ $text }}</span>
</div>