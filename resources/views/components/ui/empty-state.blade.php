@props([
    'title' => 'Belum ada data',
    'message' => 'Data akan muncul setelah tersedia.',
    'icon' => '📄',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center']) }}>
    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500">
        {{ $icon }}
    </div>

    <h3 class="text-sm font-semibold text-slate-900">
        {{ $title }}
    </h3>

    <p class="mt-1 text-sm leading-6 text-slate-500">
        {{ $message }}
    </p>

    @if (isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>