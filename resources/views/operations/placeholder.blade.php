<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-800">
                {{ $content['title'] }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $content['subtitle'] }}
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50/70 px-6 py-5">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-900 text-white">
                            <x-icon name="monitor-cog" class="h-6 w-6" />
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                {{ $content['title'] }}
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ $content['description'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 p-6 md:grid-cols-3">
                    <a
                        href="{{ route('operations.placeholder', ['page' => 'tickets']) }}"
                        class="group rounded-xl border border-slate-200 bg-white p-5 transition hover:border-slate-300 hover:shadow-sm"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                                <x-icon name="ticket-check" class="h-5 w-5" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900">
                                    Tiket Operasional
                                </h4>
                                <p class="mt-1 text-xs text-slate-500">
                                    Permintaan, gangguan, dan bantuan operasional.
                                </p>
                            </div>
                        </div>
                    </a>

                    <a
                        href="{{ route('operations.placeholder', ['page' => 'forms']) }}"
                        class="group rounded-xl border border-slate-200 bg-white p-5 transition hover:border-slate-300 hover:shadow-sm"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                                <x-icon name="clipboard-check" class="h-5 w-5" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900">
                                    Form Operasional
                                </h4>
                                <p class="mt-1 text-xs text-slate-500">
                                    Checklist dan form rutin SIM/TI.
                                </p>
                            </div>
                        </div>
                    </a>

                    <a
                        href="{{ route('operations.placeholder', ['page' => 'documents']) }}"
                        class="group rounded-xl border border-slate-200 bg-white p-5 transition hover:border-slate-300 hover:shadow-sm"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                                <x-icon name="folder-check" class="h-5 w-5" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900">
                                    Arsip Operasional
                                </h4>
                                <p class="mt-1 text-xs text-slate-500">
                                    Dokumen, foto, dan bukti operasional.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/70 px-6 py-4">
                    <p class="text-sm text-slate-500">
                        Placeholder R15-P2. CRUD, database, permission detail, public form, dan tracking tiket akan dikerjakan bertahap di step berikutnya.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>