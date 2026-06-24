@props([
    'icon' => null,
])

<a {{ $attributes->merge(['class' => 'flex w-full items-center gap-3 rounded-xl px-3 py-2 text-start text-sm font-medium text-slate-700 transition hover:bg-slate-50 focus:bg-slate-50 focus:outline-none']) }}>
    @if($icon)
        <x-icon :name="$icon" class="h-4 w-4 text-slate-400" />
    @endif

    <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>
</a>
