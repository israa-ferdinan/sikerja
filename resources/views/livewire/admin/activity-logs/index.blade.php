<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Log Aktivitas
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Pantau aktivitas penting aplikasi untuk kebutuhan audit internal dan troubleshooting.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
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

            <div class="flex items-end">
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                >
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Waktu
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            User
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Modul
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Aksi
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Deskripsi
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Detail
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">
                                <div class="font-medium text-slate-700">
                                    {{ $log->created_at?->format('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-400">
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
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    {{ $moduleLabels[$log->module] ?? str($log->module)->replace('_', ' ')->title() }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                @php
                                    $actionClass = match ($log->action) {
                                        'create' => 'bg-emerald-50 text-emerald-700',
                                        'update' => 'bg-amber-50 text-amber-700',
                                        'delete' => 'bg-rose-50 text-rose-700',
                                        'activate' => 'bg-green-50 text-green-700',
                                        'deactivate' => 'bg-slate-100 text-slate-700',
                                        'export' => 'bg-indigo-50 text-indigo-700',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp

                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $actionClass }}">
                                    {{ $actionLabels[$log->action] ?? str($log->action)->replace('_', ' ')->title() }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-600">
                                <div class="max-w-xl">
                                    {{ $log->description ?? '-' }}
                                </div>

                                @if ($log->subject_type || $log->subject_id)
                                    <div class="mt-1 text-xs text-slate-400">
                                        Target:
                                        {{ class_basename($log->subject_type) ?: '-' }}
                                        #{{ $log->subject_id ?? '-' }}
                                    </div>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                <button
                                    type="button"
                                    wire:click="openDetail({{ $log->id }})"
                                    class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700"
                                >
                                    Lihat
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada log aktivitas.
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
            wire:click.self="closeDetail"
        >
            <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white shadow-xl">
                <div class="flex items-start justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">
                            Detail Log Aktivitas
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $selectedLog->created_at?->format('d M Y H:i:s') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="rounded-lg px-2 py-1 text-slate-500 hover:bg-slate-100"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-5 p-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                User
                            </div>
                            <div class="mt-1 font-semibold text-slate-700">
                                {{ $selectedLog->user?->name ?? 'System / Unknown' }}
                            </div>
                            <div class="text-sm text-slate-500">
                                {{ $selectedLog->user?->email ?? '-' }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Role
                            </div>
                            <div class="mt-1 font-semibold text-slate-700">
                                {{ $selectedLog->role_name ?? $selectedLog->role?->name ?? '-' }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Modul
                            </div>
                            <div class="mt-1 font-semibold text-slate-700">
                                {{ $moduleLabels[$selectedLog->module] ?? str($selectedLog->module)->replace('_', ' ')->title() }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Aksi
                            </div>
                            <div class="mt-1 font-semibold text-slate-700">
                                {{ $actionLabels[$selectedLog->action] ?? str($selectedLog->action)->replace('_', ' ')->title() }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Target
                            </div>
                            <div class="mt-1 font-semibold text-slate-700">
                                {{ class_basename($selectedLog->subject_type) ?: '-' }}
                                #{{ $selectedLog->subject_id ?? '-' }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                IP Address
                            </div>
                            <div class="mt-1 font-semibold text-slate-700">
                                {{ $selectedLog->ip_address ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Deskripsi
                        </div>
                        <div class="mt-1 text-sm text-slate-700">
                            {{ $selectedLog->description ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            User Agent
                        </div>
                        <div class="mt-1 break-words text-sm text-slate-700">
                            {{ $selectedLog->user_agent ?? '-' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="rounded-xl border border-slate-200">
                            <div class="border-b border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">
                                Data Sebelum
                            </div>
                            <pre class="max-h-96 overflow-auto whitespace-pre-wrap p-4 text-xs text-slate-700">{{ $selectedLog->old_values ? json_encode($selectedLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-' }}</pre>
                        </div>

                        <div class="rounded-xl border border-slate-200">
                            <div class="border-b border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">
                                Data Sesudah
                            </div>
                            <pre class="max-h-96 overflow-auto whitespace-pre-wrap p-4 text-xs text-slate-700">{{ $selectedLog->new_values ? json_encode($selectedLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-' }}</pre>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-200 px-5 py-4">
                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>