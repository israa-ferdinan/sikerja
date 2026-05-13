@props([
    'title' => '',
    'subtitle' => '',
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
            {{ $title }}
        </h1>

        @if ($subtitle)
            <p class="mt-1 text-sm leading-6 text-slate-500">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if (isset($action))
        <div class="flex shrink-0 items-center gap-2">
            {{ $action }}
        </div>
    @endif
</div>