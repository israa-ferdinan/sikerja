<div
    id="global-confirm-modal"
    x-data="{
        open: false,
        title: 'Konfirmasi Aksi',
        message: 'Apakah Anda yakin ingin melanjutkan aksi ini?',
        confirmText: 'Ya, Lanjutkan',
        cancelText: 'Batal',
        variant: 'danger',
        onConfirm: null,

        show(payload) {
            this.title = payload.title || 'Konfirmasi Aksi'
            this.message = payload.message || 'Apakah Anda yakin ingin melanjutkan aksi ini?'
            this.confirmText = payload.confirmText || 'Ya, Lanjutkan'
            this.cancelText = payload.cancelText || 'Batal'
            this.variant = payload.variant || 'danger'
            this.onConfirm = payload.onConfirm || null
            this.open = true

            this.$nextTick(() => {
                this.$refs.cancelButton?.focus()
            })
        },

        close() {
            this.open = false
            this.onConfirm = null
        },

        confirm() {
            if (typeof this.onConfirm === 'function') {
                this.onConfirm()
            }

            this.close()
        }
    }"
    x-on:open-confirm-modal.window="show($event.detail)"
    x-on:keydown.escape.window="open && close()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center px-4 py-6"
    aria-modal="true"
    role="dialog"
>
    <div
        x-show="open"
        x-transition.opacity
        x-on:click="close()"
        class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
    ></div>

    <div
        x-show="open"
        x-transition
        class="relative w-full max-w-md overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl"
    >
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                    x-bind:class="variant === 'danger'
                        ? 'bg-rose-50 text-rose-600'
                        : 'bg-cyan-50 text-cyan-700'"
                >
                    <template x-if="variant === 'danger'">
                        <x-icon name="trash-2" class="h-6 w-6" />
                    </template>

                    <template x-if="variant !== 'danger'">
                        <x-icon name="help-circle" class="h-6 w-6" />
                    </template>
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="text-base font-bold text-slate-900" x-text="title"></h2>

                    <p class="mt-2 text-sm leading-6 text-slate-600" x-text="message"></p>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    x-ref="cancelButton"
                    x-on:click="close()"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    x-text="cancelText"
                ></button>

                <button
                    type="button"
                    x-on:click="confirm()"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition"
                    x-bind:class="variant === 'danger'
                        ? 'bg-rose-600 hover:bg-rose-700'
                        : 'bg-cyan-700 hover:bg-cyan-800'"
                    x-text="confirmText"
                ></button>
            </div>
        </div>
    </div>
</div>