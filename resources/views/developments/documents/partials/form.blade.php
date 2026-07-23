@php
    $selectedPlanId = old(
        'development_plan_id',
        $selectedPlan?->id ?? $document?->development_plan_id
    );

    $selectedUnitId = old(
        'unit_id',
        $selectedPlan?->unit_id
            ?? $document?->unit_id
            ?? ($units->count() === 1 ? $units->first()->id : null)
    );

    $inputClass = 'mt-2 block w-full rounded-xl border-slate-300 bg-white text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500';

    $errorInputClass = 'border-rose-300 focus:border-rose-500 focus:ring-rose-500';
@endphp

{{-- INFORMASI DOKUMEN --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                <x-icon name="file-text" class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-base font-semibold text-slate-900">
                    Informasi Dokumen
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Tentukan keterkaitan rencana, unit, jenis, dan visibilitas dokumen.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 lg:grid-cols-2">
        <div>
            <label
                for="development_plan_id"
                class="block text-sm font-semibold text-slate-700"
            >
                Rencana Terkait
            </label>

            <select
                id="development_plan_id"
                name="development_plan_id"
                class="{{ $inputClass }} @error('development_plan_id') {{ $errorInputClass }} @enderror"
            >
                <option value="">Dokumen Mandiri</option>

                @foreach ($plans as $plan)
                    <option
                        value="{{ $plan->id }}"
                        data-unit-id="{{ $plan->unit_id }}"
                        @selected((string) $selectedPlanId === (string) $plan->id)
                    >
                        {{ $plan->title }} — {{ $plan->unit?->name ?? '-' }}
                    </option>
                @endforeach
            </select>

            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                Kosongkan untuk membuat dokumen pengembangan mandiri.
            </p>

            @if ($selectedPlan)
                <div class="mt-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">
                        Rencana terpilih
                    </p>

                    <p class="mt-1 text-sm font-medium text-sky-900">
                        {{ $selectedPlan->title }}
                    </p>
                </div>
            @endif

            @error('development_plan_id')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="unit_id"
                class="block text-sm font-semibold text-slate-700"
            >
                Unit
                <span class="text-rose-600">*</span>
            </label>

            <select
                id="unit_id"
                name="unit_id"
                class="{{ $inputClass }} @error('unit_id') {{ $errorInputClass }} @enderror"
                required
            >
                <option value="">Pilih Unit</option>

                @foreach ($units as $unit)
                    <option
                        value="{{ $unit->id }}"
                        @selected((string) $selectedUnitId === (string) $unit->id)
                    >
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>

            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                Unit dokumen wajib sama dengan unit rencana yang dipilih.
            </p>

            @error('unit_id')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="document_type"
                class="block text-sm font-semibold text-slate-700"
            >
                Jenis Dokumen
                <span class="text-rose-600">*</span>
            </label>

            <select
                id="document_type"
                name="document_type"
                class="{{ $inputClass }} @error('document_type') {{ $errorInputClass }} @enderror"
                required
            >
                @foreach ($documentTypes as $type)
                    <option
                        value="{{ $type }}"
                        @selected(
                            old(
                                'document_type',
                                $document?->document_type ?? 'Dokumen Pendukung'
                            ) === $type
                        )
                    >
                        {{ $type }}
                    </option>
                @endforeach
            </select>

            @error('document_type')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="visibility"
                class="block text-sm font-semibold text-slate-700"
            >
                Visibilitas
                <span class="text-rose-600">*</span>
            </label>

            <select
                id="visibility"
                name="visibility"
                class="{{ $inputClass }} @error('visibility') {{ $errorInputClass }} @enderror"
                required
            >
                @foreach ($visibilities as $visibility)
                    <option
                        value="{{ $visibility }}"
                        @selected(
                            old(
                                'visibility',
                                $document?->visibility ?? 'Unit'
                            ) === $visibility
                        )
                    >
                        {{ $visibility }}
                    </option>
                @endforeach
            </select>

            <p class="mt-1.5 text-xs leading-5 text-slate-500">
                Unit dapat dilihat pegawai unit terkait. Restricted hanya dapat
                diakses oleh Admin, Kanit, dan GKM.
            </p>

            @error('visibility')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</section>

{{-- METADATA --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                <x-icon name="clipboard-list" class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-base font-semibold text-slate-900">
                    Metadata Dokumen
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Masukkan judul dan deskripsi singkat dokumen.
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-5 p-5 sm:p-6">
        <div>
            <label
                for="title"
                class="block text-sm font-semibold text-slate-700"
            >
                Judul Dokumen
                <span class="text-rose-600">*</span>
            </label>

            <input
                id="title"
                type="text"
                name="title"
                value="{{ old('title', $document?->title) }}"
                class="{{ $inputClass }} @error('title') {{ $errorInputClass }} @enderror"
                placeholder="Contoh: Proposal Pengembangan Dashboard Monitoring"
                required
            >

            @error('title')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="description"
                class="block text-sm font-semibold text-slate-700"
            >
                Deskripsi
            </label>

            <textarea
                id="description"
                name="description"
                rows="4"
                class="{{ $inputClass }} @error('description') {{ $errorInputClass }} @enderror"
                placeholder="Tuliskan keterangan singkat mengenai isi dan fungsi dokumen."
            >{{ old('description', $document?->description) }}</textarea>

            @error('description')
                <p class="mt-1.5 text-sm font-medium text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</section>

{{-- FILE --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                <x-icon name="upload-cloud" class="h-5 w-5" />
            </div>

            <div>
                <h2 class="text-base font-semibold text-slate-900">
                    File Dokumen
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Pilih file yang akan disimpan sebagai arsip pengembangan.
                </p>
            </div>
        </div>
    </div>

    <div class="p-5 sm:p-6">
        @if ($isEdit && $document?->original_name)
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-600 shadow-sm">
                    <x-icon name="file-text" class="h-5 w-5" />
                </div>

                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        File saat ini
                    </p>

                    <p class="mt-1 break-all text-sm font-semibold text-slate-900">
                        {{ $document->original_name }}
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Kosongkan input file apabila tidak ingin mengganti file.
                    </p>
                </div>
            </div>
        @endif

        <label
            for="file"
            class="block text-sm font-semibold text-slate-700"
        >
            Pilih File
            @if (! $isEdit)
                <span class="text-rose-600">*</span>
            @endif
        </label>

        <input
            id="file"
            type="file"
            name="file"
            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sky-700 hover:file:bg-sky-100 focus:border-sky-500 focus:ring-sky-500"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
            @required(! $isEdit)
        >

        <p class="mt-2 text-xs leading-5 text-slate-500">
            Format yang didukung: PDF, Word, Excel, PowerPoint, JPG, JPEG, dan PNG.
            Ukuran file maksimal 10 MB.
        </p>

        @error('file')
            <p class="mt-1.5 text-sm font-medium text-rose-600">
                {{ $message }}
            </p>
        @enderror
    </div>
</section>

{{-- ACTION --}}
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a
            href="{{ $document?->development_plan_id
                ? route('developments.plans.show', $document->development_plan_id)
                : route('developments.documents.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50"
        >
            <x-icon name="x" class="h-4 w-4" />
            Batal
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
        >
            <x-icon
                name="{{ $isEdit ? 'check-circle' : 'upload-cloud' }}"
                class="h-4 w-4"
            />

            {{ $submitLabel }}
        </button>
    </div>
</section>