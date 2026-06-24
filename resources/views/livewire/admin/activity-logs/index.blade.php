<div class="space-y-6">
    @php
        $activeFilterCount = collect([$search, $userId, $roleId, $module, $action, $startDate, $endDate])
            ->filter(fn ($value) => filled($value))
            ->count();
    @endphp

    <x-page-hero
        badge="Audit Trail"
        title="Log Aktivitas Aplikasi"
        description="Pantau aktivitas penting aplikasi untuk kebutuhan audit internal, pelacakan perubahan data, dan troubleshooting operasional."
        icon="history"
    >
        <x-slot:aside>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 shadow-sm backdrop-blur">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-cyan-100/80">
                            Total Log
                        </div>
                        <div class="mt-1 text-3xl font-bold text-white">
                            {{ number_format($logs->total()) }}
                        </div>
                        <div class="mt-1 text-xs text-slate-300">
                            Berdasarkan filter aktif saat ini
                        </div>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-slate-900 shadow-sm">
                        <x-icon name="history" class="h-5 w-5" />
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-xl border border-white/10 bg-white/10 p-3">
                        <div class="text-slate-300">Filter Aktif</div>
                        <div class="mt-1 text-lg font-bold text-white">{{ $activeFilterCount }}</div>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/10 p-3">
                        <div class="text-slate-300">Modul</div>
                        <div class="mt-1 text-lg font-bold text-white">{{ $modules->count() }}</div>
                    </div>
                </div>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white">
                    <x-icon name="search" class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Filter Log Aktivitas</h3>
                    <p class="text-xs text-slate-500">Cari berdasarkan user, role, modul, aksi, atau rentang tanggal.</p>
                </div>
            </div>

            @if ($activeFilterCount > 0)
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    <x-icon name="rotate-ccw" class="h-4 w-4" />
                    Reset Filter
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-6">
            <div class="xl:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Search
                </label>
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari deskripsi, user, modul, aksi..."
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    User
                </label>
                <select
                    wire:model.live="userId"
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name ?? $user->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Role
                </label>
                <select
                    wire:model.live="roleId"
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Modul
                </label>
                <select
                    wire:model.live="module"
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Modul</option>
                    @foreach ($modules as $moduleItem)
                        <option value="{{ $moduleItem }}">
                            {{ $moduleLabels[$moduleItem] ?? str($moduleItem)->replace('_', ' ')->title() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Aksi
                </label>
                <select
                    wire:model.live="action"
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Semua Aksi</option>
                    @foreach ($actions as $actionItem)
                        <option value="{{ $actionItem }}">
                            {{ $actionLabels[$actionItem] ?? str($actionItem)->replace('_', ' ')->title() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Dari Tanggal
                </label>
                <input
                    type="date"
                    wire:model.live="startDate"
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Sampai Tanggal
                </label>
                <input
                    type="date"
                    wire:model.live="endDate"
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div class="flex items-end xl:col-span-2">
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    <x-icon name="rotate-ccw" class="h-4 w-4" />
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Daftar Aktivitas</h3>
                <p class="text-xs text-slate-500">Menampilkan log terbaru berdasarkan filter aktif.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                <x-icon name="clock" class="h-3.5 w-3.5" />
                Urutan terbaru
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Modul</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Deskripsi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Detail</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($logs as $log)
                        @php
                            $actionClass = match ($log->action) {
                                'create' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                                'update' => 'bg-amber-50 text-amber-700 ring-amber-100',
                                'delete' => 'bg-rose-50 text-rose-700 ring-rose-100',
                                'activate' => 'bg-green-50 text-green-700 ring-green-100',
                                'deactivate' => 'bg-slate-100 text-slate-700 ring-slate-200',
                                'export' => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
                                'reset_password' => 'bg-blue-50 text-blue-700 ring-blue-100',
                                default => 'bg-slate-100 text-slate-700 ring-slate-200',
                            };
                        @endphp

                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">
                                <div class="font-semibold text-slate-700">
                                    {{ $log->created_at?->format('d M Y') }}
                                </div>
                                <div class="mt-0.5 inline-flex items-center gap-1 text-xs text-slate-400">
                                    <x-icon name="clock" class="h-3 w-3" />
                                    {{ $log->created_at?->format('H:i:s') }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <div class="font-semibold text-slate-700">
                                    {{ $log->user?->name ?? 'System / Unknown' }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ $log->role_name ?? $log->role?->name ?? '-' }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-100">
                                    {{ $moduleLabels[$log->module] ?? str($log->module)->replace('_', ' ')->title() }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $actionClass }}">
                                    {{ $actionLabels[$log->action] ?? str($log->action)->replace('_', ' ')->title() }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-600">
                                <div class="max-w-xl leading-6">
                                    {{ $log->description ?? '-' }}
                                </div>

                                @if ($log->subject_type || $log->subject_id)
                                    <div class="mt-1 text-xs text-slate-400">
                                        Target: {{ class_basename($log->subject_type) ?: '-' }} #{{ $log->subject_id ?? '-' }}
                                    </div>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                <button
                                    type="button"
                                    wire:click="openDetail({{ $log->id }})"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-700"
                                >
                                    <x-icon name="info" class="h-3.5 w-3.5" />
                                    Lihat
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="history" class="h-6 w-6" />
                                    </div>
                                    <div class="mt-3 font-semibold text-slate-700">Belum ada log aktivitas</div>
                                    <div class="mt-1 text-xs text-slate-500">Coba ubah filter atau tunggu sampai ada aktivitas baru yang tercatat.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $logs->links() }}
        </div>
    </div>

    @if ($showDetailModal && $selectedLog)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
            wire:click.self="closeDetail"
        >
            <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-3xl bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-white">
                            <x-icon name="history" class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">
                                Detail Log Aktivitas
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $selectedLog->created_at?->format('d M Y H:i:s') }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100"
                    >
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-5 p-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">User</div>
                            <div class="mt-1 font-semibold text-slate-700">{{ $selectedLog->user?->name ?? 'System / Unknown' }}</div>
                            <div class="text-sm text-slate-500">{{ $selectedLog->user?->email ?? '-' }}</div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Role</div>
                            <div class="mt-1 font-semibold text-slate-700">{{ $selectedLog->role_name ?? $selectedLog->role?->name ?? '-' }}</div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Modul</div>
                            <div class="mt-1 font-semibold text-slate-700">{{ $moduleLabels[$selectedLog->module] ?? str($selectedLog->module)->replace('_', ' ')->title() }}</div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Aksi</div>
                            <div class="mt-1 font-semibold text-slate-700">{{ $actionLabels[$selectedLog->action] ?? str($selectedLog->action)->replace('_', ' ')->title() }}</div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Target</div>
                            <div class="mt-1 font-semibold text-slate-700">
                                {{ class_basename($selectedLog->subject_type) ?: '-' }} #{{ $selectedLog->subject_id ?? '-' }}
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">IP Address</div>
                            <div class="mt-1 font-semibold text-slate-700">{{ $selectedLog->ip_address ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Deskripsi</div>
                        <div class="mt-1 text-sm leading-6 text-slate-700">{{ $selectedLog->description ?? '-' }}</div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">User Agent</div>
                        <div class="mt-1 break-words text-sm leading-6 text-slate-700">{{ $selectedLog->user_agent ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="overflow-hidden rounded-2xl border border-slate-200">
                            <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                                Data Sebelum
                            </div>
                            <pre class="max-h-96 overflow-auto whitespace-pre-wrap p-4 text-xs leading-5 text-slate-700">{{ $selectedLog->old_values ? json_encode($selectedLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-' }}</pre>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-slate-200">
                            <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                                Data Sesudah
                            </div>
                            <pre class="max-h-96 overflow-auto whitespace-pre-wrap p-4 text-xs leading-5 text-slate-700">{{ $selectedLog->new_values ? json_encode($selectedLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-' }}</pre>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-200 px-5 py-4">
                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700"
                    >
                        <x-icon name="x" class="h-4 w-4" />
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
