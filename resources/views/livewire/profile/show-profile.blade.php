<div class="space-y-6">
    @if (session('warning'))
        <div
            x-data="{ open: true }"
            x-show="open"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4 backdrop-blur-sm"
        >
            <div
                x-show="open"
                x-transition.scale.origin.center
                @click.outside="open = false"
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
            >
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-yellow-100 text-yellow-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-bold text-gray-900">
                            Ganti Password Terlebih Dahulu
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-600">
                            {{ session('warning') }}
                        </p>

                        <p class="mt-2 text-sm leading-6 text-gray-600">
                            Setelah password berhasil diperbarui, Anda bisa kembali mengakses menu aplikasi seperti biasa.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        type="button"
                        @click="open = false"
                        class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    @endif
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="mt-1 text-sm text-gray-500">
            Lihat informasi akun dan ubah password login Anda.
        </p>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            {{ session('warning') }}
        </div>
    @endif

    @if ($user->must_change_password)
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            <strong>Perhatian:</strong> Anda wajib mengganti password terlebih dahulu sebelum menggunakan menu aplikasi lainnya.
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">
                Informasi Akun
            </h2>

            <div class="mt-5 space-y-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Nama User</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $user->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Username</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $user->username ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Email</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $user->email ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Role Aplikasi</p>
                    <p class="mt-1 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold capitalize text-blue-700">
                        {{ $user->role?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Status Akun</p>
                    @if($user->is_active)
                        <span class="mt-1 inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                            Aktif
                        </span>
                    @else
                        <span class="mt-1 inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Terakhir Login</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">
                        {{ $user->last_login_at?->format('d M Y H:i') ?? 'Belum tercatat' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Terakhir Ganti Password</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">
                        {{ $user->password_changed_at?->format('d M Y H:i') ?? 'Belum pernah ganti password' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">
                Informasi Pegawai
            </h2>

            <div class="mt-5 space-y-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Nama Pegawai</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $employee?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">NIP</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $employee?->nip ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Unit</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $employee?->unit?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Jabatan Pekerjaan</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">
                        {{ $employee?->jobPosition?->name ?? $employee?->position ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">No. HP</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $employee?->phone ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Email Pegawai</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">{{ $employee?->email ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Tanda Tangan Digital Kanit
                </h2>

                <p class="mt-1 text-sm leading-6 text-gray-500">
                    Upload gambar tanda tangan untuk digunakan pada finalisasi dan export laporan bulanan.
                    Format yang didukung: PNG, JPG, JPEG, atau WEBP. Maksimal 1 MB.
                </p>
            </div>

            @if ($employee?->signature_path)
                <span class="inline-flex w-fit rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                    Sudah Upload
                </span>
            @else
                <span class="inline-flex w-fit rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700">
                    Belum Upload
                </span>
            @endif
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                    Preview Tanda Tangan Saat Ini
                </p>

                @if ($employee?->signature_path)
                    <div class="mt-4 flex min-h-36 items-center justify-center rounded-xl border border-gray-200 bg-white p-4">
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($employee->signature_path) }}"
                            alt="Tanda tangan {{ $employee?->name }}"
                            class="max-h-28 max-w-full object-contain"
                        >
                    </div>

                    <button
                        type="button"
                        wire:click="deleteSignature"
                        wire:confirm="Hapus tanda tangan yang tersimpan?"
                        wire:loading.attr="disabled"
                        wire:target="deleteSignature"
                        class="mt-4 inline-flex items-center rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="deleteSignature">
                            Hapus Tanda Tangan
                        </span>
                        <span wire:loading wire:target="deleteSignature">
                            Menghapus...
                        </span>
                    </button>
                @else
                    <div class="mt-4 flex min-h-36 items-center justify-center rounded-xl border border-gray-200 bg-white p-4 text-center">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">
                                Belum ada tanda tangan
                            </p>
                            <p class="mt-1 text-xs leading-5 text-gray-500">
                                Tanda tangan wajib tersedia sebelum Kanit melakukan finalisasi laporan bulanan.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <form wire:submit.prevent="updateSignature" class="rounded-2xl border border-gray-200 bg-white p-5">
                <label class="block text-sm font-medium text-gray-700">
                    Upload Tanda Tangan Baru
                </label>

                <input
                    type="file"
                    wire:model="signature"
                    accept="image/png,image/jpeg,image/jpg,image/webp"
                    class="mt-2 block w-full rounded-xl border border-gray-300 text-sm text-gray-700 shadow-sm file:mr-4 file:border-0 file:bg-blue-50 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
                >

                @error('signature')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div wire:loading wire:target="signature" class="mt-3 text-sm text-gray-500">
                    Memproses preview tanda tangan...
                </div>

                @if ($signature)
                    <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Preview Upload Baru
                        </p>

                        <div class="mt-3 flex min-h-28 items-center justify-center rounded-xl bg-white p-3">
                            <img
                                src="{{ $signature->temporaryUrl() }}"
                                alt="Preview tanda tangan baru"
                                class="max-h-24 max-w-full object-contain"
                            >
                        </div>
                    </div>
                @endif

                <div class="mt-5 flex items-center gap-3">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="updateSignature,signature"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="updateSignature">
                            Simpan Tanda Tangan
                        </span>
                        <span wire:loading wire:target="updateSignature">
                            Menyimpan...
                        </span>
                    </button>

                    <span
                        wire:loading
                        wire:target="updateSignature"
                        class="text-sm text-gray-500"
                    >
                        Mengupload tanda tangan...
                    </span>
                </div>

                <p class="mt-4 text-xs leading-5 text-gray-500">
                    Disarankan memakai gambar tanda tangan dengan background transparan atau putih agar rapi saat masuk ke export Excel.
                </p>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-900">
            Ubah Password
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Gunakan password yang kuat dan jangan bagikan kepada orang lain.
        </p>

        <form wire:submit.prevent="updatePassword" class="mt-6 max-w-xl space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Password Lama
                </label>
                <input
                    type="password"
                    wire:model.defer="current_password"
                    class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    autocomplete="current-password"
                >
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Password Baru
                </label>
                <input
                    type="password"
                    wire:model.defer="password"
                    class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    autocomplete="new-password"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Konfirmasi Password Baru
                </label>
                <input
                    type="password"
                    wire:model.defer="password_confirmation"
                    class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    autocomplete="new-password"
                >
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="updatePassword"
                    class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="updatePassword">
                        Simpan Password
                    </span>
                    <span wire:loading wire:target="updatePassword">
                        Menyimpan...
                    </span>
                </button>

                <span
                    wire:loading
                    wire:target="updatePassword"
                    class="text-sm text-gray-500"
                >
                    Memproses perubahan password...
                </span>
            </div>
        </form>
    </div>
</div>