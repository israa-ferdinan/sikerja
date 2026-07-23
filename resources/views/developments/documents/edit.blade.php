<x-app-layout>
    <div class="w-full space-y-6">

        {{-- HERO --}}
        <section class="overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 shadow-lg shadow-slate-900/10">
            <div class="flex min-h-[210px] flex-col gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:flex-row lg:items-center lg:justify-between lg:px-10 lg:py-11">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-cyan-100">
                        <x-icon name="file-text" class="h-4 w-4" />
                        Pengembangan
                    </div>

                    <h1 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Edit Dokumen Pengembangan
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 sm:text-base">
                        Perbarui metadata, visibilitas, keterkaitan rencana,
                        atau ganti file dokumen pengembangan.
                    </p>
                </div>

                <div class="shrink-0 lg:pl-8">
                    <a
                        href="{{ $document->development_plan_id
                            ? route('developments.plans.show', $document->development_plan_id)
                            : route('developments.documents.index') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:border-white/30 hover:bg-white/15 sm:w-auto"
                    >
                        <x-icon name="arrow-left" class="h-4 w-4" />
                        Kembali
                    </a>
                </div>
            </div>
        </section>

        @if ($errors->any())
            <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 shadow-sm">
                <x-icon name="x-circle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />

                <div>
                    <p class="font-semibold">Terjadi Kesalahan</p>
                    <p class="mt-1 leading-6">
                        Periksa kembali field yang ditandai sebelum menyimpan perubahan.
                    </p>
                </div>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('developments.documents.update', $document) }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            @include('developments.documents.partials.form', [
                'document' => $document,
                'selectedPlan' => $selectedPlan,
                'submitLabel' => 'Simpan Perubahan',
                'isEdit' => true,
            ])
        </form>
    </div>
</x-app-layout>