<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-800">
                Notifikasi
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Daftar pemberitahuan dan penugasan yang terkait dengan akun Anda.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Total
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ $summary['total'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                        Belum Dibaca
                    </p>
                    <p class="mt-2 text-3xl font-bold text-blue-950">
                        {{ $summary['unread'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                        Sudah Dibaca
                    </p>
                    <p class="mt-2 text-3xl font-bold text-emerald-950">
                        {{ $summary['read'] }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('notifications.index') }}"
                        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition {{ blank(request('status')) ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
                    >
                        Semua
                    </a>

                    <a
                        href="{{ route('notifications.index', ['status' => 'unread']) }}"
                        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition {{ request('status') === 'unread' ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
                    >
                        Belum Dibaca
                        @if($summary['unread'] > 0)
                            <span class="ml-2 inline-flex rounded-full bg-white/20 px-2 py-0.5 text-xs">
                                {{ $summary['unread'] }}
                            </span>
                        @endif
                    </a>

                    <a
                        href="{{ route('notifications.index', ['status' => 'read']) }}"
                        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition {{ request('status') === 'read' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
                    >
                        Sudah Dibaca
                    </a>
                </div>
                <form method="GET" action="{{ route('notifications.index') }}" class="grid gap-3 md:grid-cols-4">
                    <div>
                        <label for="status" class="mb-1 block text-xs font-medium text-slate-600">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="">Semua Status</option>
                            <option value="unread" @selected(request('status') === 'unread')>
                                Belum Dibaca
                            </option>
                            <option value="read" @selected(request('status') === 'read')>
                                Sudah Dibaca
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="module" class="mb-1 block text-xs font-medium text-slate-600">
                            Modul
                        </label>
                        <select
                            id="module"
                            name="module"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="">Semua Modul</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" @selected(request('module') === $module)>
                                    {{ $module }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('notifications.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    </div>

                    <div class="flex items-end justify-end">
                        <button
                            type="submit"
                            form="mark-all-read-form"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Tandai Semua Dibaca
                        </button>
                    </div>
                </form>

                <form id="mark-all-read-form" method="POST" action="{{ route('notifications.read-all') }}" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            </div>

            <div class="space-y-3">
                @forelse($notifications as $notification)
                    <div class="rounded-2xl border {{ $notification->isUnread() ? 'border-blue-200 bg-blue-50' : 'border-slate-200 bg-white' }} p-5 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($notification->module)
                                        <span class="inline-flex rounded-full bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white">
                                            {{ $notification->module }}
                                        </span>
                                    @endif

                                    @if($notification->isUnread())
                                        <span class="inline-flex rounded-full bg-blue-600 px-2.5 py-1 text-xs font-semibold text-white">
                                            Baru
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            Dibaca
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-3 text-base font-semibold text-slate-900">
                                    {{ $notification->title }}
                                </h3>

                                @if($notification->message)
                                    <p class="mt-2 text-sm leading-6 text-slate-600">
                                        {{ $notification->message }}
                                    </p>
                                @endif

                                <p class="mt-2 text-xs text-slate-500">
                                    {{ $notification->created_at?->format('d M Y H:i') }}
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-wrap gap-2">
                                @if($notification->url)
                                    <a
                                        href="{{ route('notifications.open', $notification) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                                    >
                                        Buka
                                    </a>
                                @endif

                                @if($notification->isUnread())
                                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                        >
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                            <x-icon name="bell" class="h-6 w-6" />
                        </div>

                        <h3 class="mt-3 text-sm font-semibold text-slate-900">
                            @if(request('status') === 'unread')
                                Tidak ada notifikasi belum dibaca
                            @elseif(request('status') === 'read')
                                Tidak ada notifikasi sudah dibaca
                            @else
                                Belum ada notifikasi
                            @endif
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            @if(request('status') === 'unread')
                                Semua notifikasi Anda sudah dibaca.
                            @elseif(request('status') === 'read')
                                Notifikasi yang sudah dibaca akan muncul di sini.
                            @else
                                Notifikasi penugasan dan informasi aplikasi akan muncul di sini.
                            @endif
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Notifikasi penugasan dan informasi aplikasi akan muncul di sini.
                        </p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div>
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>