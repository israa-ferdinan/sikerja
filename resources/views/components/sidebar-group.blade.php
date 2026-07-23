@props([
    'title',
    'icon' => 'folder',
    'active' => false,
])

<div
    x-data="{ open: @js($active) }"
    class="rounded-2xl border border-slate-100 bg-slate-50/70 p-2"
>
    <button
        type="button"
        class="flex w-full items-center justify-between rounded-xl px-2 py-2 text-left text-xs font-bold uppercase tracking-wide transition
            {{ $active ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:bg-white hover:text-slate-700' }}"
        @click="open = !open"
    >
        <span class="flex items-center gap-2">
            <x-icon :name="$icon" class="h-4 w-4" />
            {{ $title }}
        </span>

        <x-icon
            name="chevron-down"
            class="h-4 w-4 transition-transform duration-200"
            x-bind:class="open ? 'rotate-180' : ''"
        />
    </button>

    <div
        x-show="open"
        x-collapse
        class="mt-1 space-y-1"
    >
        {{ $slot }}
    </div>
</div>