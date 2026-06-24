@props([
    'title',
    'description' => null,
    'icon' => 'file-text',
])

<section {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm shadow-slate-900/5 backdrop-blur-xl sm:p-6']) }}>
    <div class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-sky-100/70 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-20 left-10 h-44 w-44 rounded-full bg-slate-200/70 blur-3xl"></div>

    <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-lg shadow-slate-900/10">
                <x-icon :name="$icon" class="h-6 w-6" />
            </div>

            <div class="min-w-0">
                <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                    {{ $title }}
                </h1>

                @if($description)
                    <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-600">
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>

        @isset($actions)
            <div class="relative flex shrink-0 flex-wrap items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
