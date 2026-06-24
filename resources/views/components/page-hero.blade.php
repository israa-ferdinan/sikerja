@props([
    'badge' => null,
    'title' => '',
    'description' => '',
    'icon' => 'info',
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 p-6 text-white shadow-sm']) }}>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:items-center">
        <div class="lg:col-span-8">
            @if ($badge)
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-cyan-100">
                    <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                    {{ $badge }}
                </div>
            @endif

            <h2 class="{{ $badge ? 'mt-4' : '' }} text-2xl font-bold tracking-tight text-white">
                {{ $title }}
            </h2>

            @if ($description)
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                    {{ $description }}
                </p>
            @endif
        </div>

        @isset($aside)
            <div class="lg:col-span-4">
                {{ $aside }}
            </div>
        @else
            <div class="hidden lg:flex lg:col-span-4 lg:justify-end">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-cyan-200 shadow-sm backdrop-blur">
                    <x-icon :name="$icon" class="h-7 w-7" />
                </div>
            </div>
        @endisset
    </div>
</div>
