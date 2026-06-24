<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        try {
            $this->validate();

            $this->form->authenticate();

            Session::regenerate();

            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: false);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('login-failed');

            throw $e;
        } catch (\Throwable $e) {
            $this->dispatch('login-failed');

            throw $e;
        }
    }
}; ?>
<div  x-data="{ loggingIn: false }" x-on:login-failed.window="loggingIn = false" class="min-h-screen bg-slate-100">
    {{-- Login Loading Overlay --}}
    <div
        x-show="loggingIn"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 px-4 backdrop-blur-sm"
    >
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
            </div>

            <h3 class="text-base font-semibold text-slate-800">
                Memproses Login
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Mohon tunggu sebentar, sistem sedang memverifikasi akun Anda.
            </p>
        </div>
    </div>

    <div class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid w-full max-w-6xl overflow-hidden rounded-3xl bg-white shadow-xl lg:grid-cols-2">
            {{-- Left Panel --}}
            <div class="relative hidden bg-gradient-to-br from-blue-700 via-blue-800 to-slate-900 p-10 text-white lg:block">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white"></div>
                    <div class="absolute -bottom-24 -right-24 h-80 w-80 rounded-full bg-white"></div>
                </div>

                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <div class="mb-8 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z" />
                            </svg>
                        </div>

                        <h1 class="text-3xl font-bold leading-tight">
                            SIPALING KERJA — Sistem Informasi Pelaporan Aktivitas Lingkup Kerja 
                        </h1>

                        <p class="mt-4 max-w-md text-sm leading-6 text-blue-100">
                            Sistem pencatatan, monitoring, dan rekap laporan kerja harian pegawai secara lebih rapi, cepat, dan terstruktur.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-xl bg-white/15">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-white">
                                        Input laporan lebih cepat
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-blue-100">
                                        Mendukung template laporan, master data, dan dokumentasi foto.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-blue-100">
                            © {{ date('Y') }} Sistem Informasi Manajemen / Teknologi Informatika
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right Panel --}}
            <div class="flex items-center justify-center px-6 py-10 sm:px-10 lg:px-12">
                <div class="w-full max-w-md">
                    {{-- Mobile Logo --}}
                    <div class="mb-8 text-center lg:hidden">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z" />
                            </svg>
                        </div>

                        <h1 class="text-xl font-bold text-slate-800">
                            Aplikasi Laporan Kerja Kantor
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            Silakan masuk untuk melanjutkan
                        </p>
                    </div>

                    {{-- Desktop Heading --}}
                    <div class="mb-8 hidden lg:block">
                        <p class="text-lg font-bold tracking-tight text-blue-600">
                            Selamat Datang
                        </p>

                        <h2 class="mt-2 text-2xl font-bold text-slate-900">
                            Masuk ke Akun Anda
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Gunakan username yang sudah terdaftar untuk mengakses dashboard laporan kerja.
                        </p>
                    </div>

                    {{-- Error Global --}}
                    @if (session()->has('error'))
                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Info akses foto laporan --}}
                    @if(session()->has('url.intended') && str_contains(session('url.intended'), '/reports/photos/'))
                        <div class="mb-5 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                    <x-icon name="lock-keyhole" class="h-5 w-5" />
                                </div>

                                <div>
                                    <p class="font-bold">
                                        Akses foto laporan membutuhkan login
                                    </p>

                                    <p class="mt-1 leading-6 text-blue-700">
                                        Silakan masuk terlebih dahulu. Setelah login berhasil, foto laporan yang Anda buka akan ditampilkan otomatis.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="login" x-on:submit="loggingIn = true" class="space-y-5">
                        {{-- Login Field --}}
                        <div>
                            <label for="login" class="mb-2 block text-sm font-medium text-slate-700">
                                Email / Username
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6.75A2.75 2.75 0 0 1 6.75 4h10.5A2.75 2.75 0 0 1 20 6.75v10.5A2.75 2.75 0 0 1 17.25 20H6.75A2.75 2.75 0 0 1 4 17.25V6.75Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 7 7 6 7-6" />
                                    </svg>
                                </div>

                                <input
                                    id="login"
                                    type="text"
                                    wire:model.defer="form.email"
                                    wire:loading.attr="disabled"
                                    wire:target="login"
                                    x-bind:disabled="loggingIn"
                                    autocomplete="username"
                                    class="block w-full rounded-2xl border border-slate-300 bg-white py-3 pl-12 pr-4 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                                    placeholder="Masukkan email atau username"
                                >
                            </div>

                            @error('form.email')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password Field --}}
                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
                                Password
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V8a4.5 4.5 0 0 0-9 0v2.5" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 10.5h10.5A1.75 1.75 0 0 1 19 12.25v6A1.75 1.75 0 0 1 17.25 20H6.75A1.75 1.75 0 0 1 5 18.25v-6a1.75 1.75 0 0 1 1.75-1.75Z" />
                                    </svg>
                                </div>

                                <input
                                    id="password"
                                    type="password"
                                    wire:model.defer="form.password"
                                    wire:loading.attr="disabled"
                                    wire:target="login"
                                    x-bind:disabled="loggingIn"
                                    autocomplete="current-password"
                                    class="block w-full rounded-2xl border border-slate-300 bg-white py-3 pl-12 pr-4 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                                    placeholder="Masukkan password"
                                >
                            </div>

                            @error('form.password')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="login"
                            x-bind:disabled="loggingIn"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            <span wire:loading.remove wire:target="login">
                                Masuk
                            </span>

                            <span wire:loading.flex wire:target="login" class="items-center gap-2">
                                <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                                Memproses...
                            </span>
                        </button>
                    </form>

                    <div class="mt-8 border-t border-slate-200 pt-5 text-center">
                        <p class="text-xs leading-5 text-slate-500">
                            Akses hanya untuk pengguna yang terdaftar.
                            <br>
                            Hubungi administrator jika mengalami kendala login.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
<script>
    Livewire.hook('request', ({ succeed, fail }) => {
        succeed(() => {
            setTimeout(() => {
                const hasError = document.querySelector('[data-login-error="true"]');

                if (hasError) {
                    Alpine.store('loginLoading', false);
                }
            }, 150);
        });

        fail(() => {
            Alpine.store('loginLoading', false);
        });
    });
</script>
@endscript