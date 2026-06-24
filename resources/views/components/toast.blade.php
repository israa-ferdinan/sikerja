@php
    $sessionToast = session('toast');

    $initialToasts = collect([
        is_array($sessionToast) ? $sessionToast : null,
        blank($sessionToast) && session('success') ? ['type' => 'success', 'message' => session('success')] : null,
        blank($sessionToast) && session('error') ? ['type' => 'error', 'message' => session('error')] : null,
        blank($sessionToast) && session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
        blank($sessionToast) && session('info') ? ['type' => 'info', 'message' => session('info')] : null,
        blank($sessionToast) && session('status') ? ['type' => 'success', 'message' => session('status')] : null,
    ])->filter()->values();
@endphp

<div
    x-data="sipalingToast(@js($initialToasts))"
    x-init="init()"
    class="pointer-events-none fixed inset-x-0 top-4 z-[80] flex flex-col items-end gap-3 px-4 sm:inset-x-auto sm:right-4 sm:w-full sm:max-w-sm"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transform ease-out duration-200 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto w-full overflow-hidden rounded-2xl border bg-white shadow-xl shadow-slate-900/10 ring-1 ring-black/5"
            :class="borderClass(toast.type)"
        >
            <div class="flex gap-3 p-4">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl"
                    :class="iconWrapClass(toast.type)"
                >
                    <template x-if="toast.type === 'success'">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </template>
                    <template x-if="toast.type === 'error'">
                        <x-icon name="x-circle" class="h-5 w-5" />
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <x-icon name="alert-circle" class="h-5 w-5" />
                    </template>
                    <template x-if="!['success', 'error', 'warning'].includes(toast.type)">
                        <x-icon name="info" class="h-5 w-5" />
                    </template>
                </div>

                <div class="min-w-0 flex-1 pt-0.5">
                    <p class="text-sm font-semibold text-slate-900" x-text="title(toast.type)"></p>
                    <p class="mt-0.5 text-sm leading-5 text-slate-600" x-text="toast.message"></p>
                </div>

                <button
                    type="button"
                    class="-mr-1 rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    @click="remove(toast.id)"
                >
                    <span class="sr-only">Tutup notifikasi</span>
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>
        </div>
    </template>
</div>

<script>
    function sipalingToast(initialToasts = []) {
        return {
            toasts: [],
            recentKeys: new Map(),
            init() {
                initialToasts.forEach((toast) => this.add(toast));

                window.addEventListener('toast', (event) => {
                    this.add(event.detail || {});
                });
            },
            add(toast) {
                const type = toast.type || 'info';
                const message = toast.message || 'Aksi berhasil diproses.';
                const key = `${type}:${message}`;
                const now = Date.now();
                const lastShownAt = this.recentKeys.get(key) || 0;

                if (now - lastShownAt < 1200) {
                    return;
                }

                this.recentKeys.set(key, now);

                const id = now + Math.random();

                this.toasts.push({
                    id,
                    type,
                    message,
                    visible: true,
                });

                setTimeout(() => this.remove(id), toast.timeout || 4500);

                setTimeout(() => {
                    if (this.recentKeys.get(key) === now) {
                        this.recentKeys.delete(key);
                    }
                }, 2000);
            },
            remove(id) {
                const toast = this.toasts.find((item) => item.id === id);

                if (! toast) {
                    return;
                }

                toast.visible = false;

                setTimeout(() => {
                    this.toasts = this.toasts.filter((item) => item.id !== id);
                }, 180);
            },
            title(type) {
                return {
                    success: 'Berhasil',
                    error: 'Gagal',
                    warning: 'Perhatian',
                    info: 'Informasi',
                }[type] || 'Informasi';
            },
            borderClass(type) {
                return {
                    success: 'border-emerald-100',
                    error: 'border-red-100',
                    warning: 'border-amber-100',
                    info: 'border-sky-100',
                }[type] || 'border-sky-100';
            },
            iconWrapClass(type) {
                return {
                    success: 'bg-emerald-50 text-emerald-600',
                    error: 'bg-red-50 text-red-600',
                    warning: 'bg-amber-50 text-amber-600',
                    info: 'bg-sky-50 text-sky-600',
                }[type] || 'bg-sky-50 text-sky-600';
            },
        };
    }
</script>
