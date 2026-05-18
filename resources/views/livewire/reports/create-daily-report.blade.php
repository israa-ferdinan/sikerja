<div class="relative z-10 p-4 sm:p-6">
    @if ($missingEmployee)
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            Akun Anda belum terhubung dengan data pegawai. Silakan hubungi admin.
        </div>
    @elseif ($missingUnit)
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            Data pegawai Anda belum memiliki unit kerja. Silakan hubungi admin.
        </div>
    @else
    <div class="relative z-10 mx-auto max-w-5xl space-y-6">
        <x-ui.page-header
            title="Input Laporan Kerja Harian"
            subtitle="Isi laporan pekerjaan harian berdasarkan kegiatan, tupoksi, server, atau aplikasi yang dikerjakan."
        />

        @if (session()->has('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6">

            {{-- Template Cepat --}}
            <x-ui.card>
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Template Cepat
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Pilih template atau clone laporan terakhir agar pengisian laporan lebih cepat.
                        </p>
                    </div>

                    <x-ui.badge variant="primary">
                        Input cepat
                    </x-ui.badge>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Template Deskripsi
                    </label>

                    <div class="grid gap-4 md:grid-cols-12 md:items-center">
                        <div class="md:col-span-8">
                            <select
                                    wire:model.change="form.template_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                >
                                <option value="">-- Pilih Template Laporan --</option>

                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">
                                        {{ $template->title }}
                                        @if($template->category)
                                            - {{ $template->category }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <button
                                type="button"
                                wire:click="cloneLastReport"
                                wire:loading.attr="disabled"
                                wire:target="cloneLastReport"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200 disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                <span wire:loading.remove wire:target="cloneLastReport">
                                    Clone Laporan Terakhir
                                </span>

                                <span wire:loading.flex wire:target="cloneLastReport" class="items-center gap-2">
                                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                                    Mengambil...
                                </span>
                            </button>
                        </div>
                    </div>

                    <p class="mt-1.5 text-xs leading-5 text-slate-500">
                        Template akan membantu mengisi judul dan deskripsi secara otomatis.
                    </p>
                </div>
            </x-ui.card>

            {{-- Informasi Laporan --}}
            <x-ui.card>
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Informasi Laporan
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Pilih tanggal, tupoksi, server, dan aplikasi yang berkaitan dengan pekerjaan.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Tanggal Laporan <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            wire:model.change="form.report_date"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('form.report_date')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Tupoksi
                        </label>

                        <select
                            wire:model.change="form.duty_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            @disabled($duties->isEmpty())                       
                            >  
                            <option value="">
                                {{ $duties->isEmpty() ? 'Belum ada tupoksi yang ditugaskan' : 'Pilih Tupoksi' }}
                            </option>

                            @foreach ($duties as $duty)
                                <option value="{{ $duty->id }}">
                                    {{ $duty->name }}
                                </option>
                            @endforeach
                        </select>

                        @if ($duties->isEmpty())
                            <p class="mt-2 rounded-lg bg-yellow-50 px-3 py-2 text-sm text-yellow-700">
                                Belum ada tupoksi yang ditugaskan ke akun Anda. Silakan hubungi admin.
                            </p>
                        @endif

                        @error('duty_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @error('form.duty_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Server
                        </label>

                        <select
                            wire:model.change="form.server_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >
                            <option value="">-- Pilih Server --</option>

                            @foreach ($servers as $server)
                                <option value="{{ $server->id }}">
                                    {{ $server->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('form.server_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Aplikasi
                        </label>

                        <select
                            wire:model.change="form.application_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 disabled:bg-slate-100 disabled:text-slate-500"
                            @disabled(empty($form['server_id']))
                        >
                            <option value="">
                                {{ !empty($form['server_id']) ? '-- Pilih Aplikasi --' : 'Pilih server terlebih dahulu' }}
                            </option>

                            @foreach ($applications as $application)
                                <option value="{{ $application->id }}">
                                    {{ $application->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('form.application_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-ui.card>

            {{-- Detail Pekerjaan --}}
            <x-ui.card>
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Detail Pekerjaan
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Tulis ringkasan kegiatan dan catatan hasil pekerjaan secara jelas.
                    </p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Judul Kegiatan <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            wire:model.blur="form.title"
                            placeholder="Contoh: Pengecekan server aplikasi SIAKAD"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('form.title')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Deskripsi Kegiatan <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            wire:model.blur="form.description"
                            rows="5"
                            placeholder="Tuliskan detail pekerjaan yang dilakukan hari ini..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        ></textarea>

                        @error('form.description')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Catatan Tambahan
                        </label>

                        <textarea
                            wire:model.blur="form.notes"
                            rows="3"
                            placeholder="Opsional. Contoh: kendala, tindak lanjut, atau informasi tambahan."
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        ></textarea>

                        @error('form.notes')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-ui.card>

            {{-- Dokumentasi Foto --}}
            <x-ui.card>
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Dokumentasi Foto
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Upload foto pendukung laporan. Maksimal 5 foto, 5 MB per foto.
                        </p>
                    </div>

                    <x-ui.badge variant="neutral">
                        {{ count($photos) }}/5 foto
                    </x-ui.badge>
                </div>

                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5">
                    <label class="flex cursor-pointer flex-col items-center justify-center text-center">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5" />
                            </svg>
                        </div>

                        <span class="text-sm font-semibold text-slate-700">
                            Klik untuk upload foto
                        </span>

                        <span class="mt-1 text-xs leading-5 text-slate-500">
                            Bisa pilih beberapa foto sekaligus, atau tambah foto satu per satu.
                        </span>

                        <input
                            wire:key="photo-input-{{ $photoInputKey }}"
                            type="file"
                            wire:model="newPhotos"
                            multiple
                            accept="image/*"
                            class="hidden"
                        >
                    </label>

                    <div class="mt-4">
                        <x-ui.loading target="newPhotos" text="Menambahkan foto..." />
                    </div>

                    @error('photos')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @error('photos.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @error('newPhotos.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if (count($photos) > 0)
                    <div class="mt-5">
                        <h3 class="mb-3 text-sm font-bold text-slate-800">
                            Preview Foto
                        </h3>

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                            @foreach ($photos as $index => $photo)
                                <div
                                    wire:key="photo-preview-{{ $index }}-{{ $photo->getFilename() }}"
                                    class="relative overflow-visible"
                                >
                                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                        <img
                                            src="{{ $photo->temporaryUrl() }}"
                                            class="block h-32 w-full object-cover"
                                            alt="Preview foto {{ $index + 1 }}"
                                        >

                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-3 pb-2 pt-8">
                                            <span class="text-xs font-semibold text-white">
                                                Foto {{ $index + 1 }}
                                            </span>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="removePhoto({{ $index }})"
                                            wire:confirm="Hapus foto ini?"
                                            class="absolute right-2 top-2 z-30 flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-lg ring-2 ring-white transition hover:bg-red-700 sm:opacity-0 sm:group-hover:opacity-100"
                                            title="Hapus foto"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </x-ui.card>

            {{-- Action --}}
            <div class="sticky bottom-0 z-20 -mx-4 border-t border-slate-200 bg-white/90 px-4 py-4 backdrop-blur sm:static sm:mx-0 sm:rounded-2xl sm:border sm:border-slate-200 sm:bg-white sm:shadow-sm">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="{{ route('pegawai.dashboard') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                    >
                        Kembali
                    </a>

                    <button
                        type="submit"
                        @disabled($duties->isEmpty())
                        wire:loading.attr="disabled"
                        wire:target="save,newPhotos"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <span wire:loading.remove wire:target="save">
                            Simpan Laporan
                        </span>

                        <span wire:loading.flex wire:target="save" class="items-center gap-2">
                            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>

        </form>
    </div>
    @endif
</div>