<header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-3 border-b border-white/70 bg-white/85 px-4 shadow-sm backdrop-blur-xl sm:gap-x-4 sm:px-6 lg:px-8">
    {{-- Mobile menu button --}}
    <button
        type="button"
        class="inline-flex shrink-0 items-center justify-center rounded-xl p-2 text-slate-700 transition hover:bg-slate-100 lg:hidden"
        @click="sidebarOpen = true"
    >
        <span class="sr-only">Open sidebar</span>
        <x-icon name="menu" class="h-5 w-5" />
    </button>

    <div class="h-6 w-px shrink-0 bg-slate-200 lg:hidden"></div>

    <div class="flex min-w-0 flex-1 items-center justify-between gap-3">
        <div class="min-w-0 flex-1">
            <h2 class="truncate text-sm font-semibold text-slate-800">
                Aplikasi Laporan Kerja Kantor
            </h2>
            <p class="truncate text-xs text-slate-500">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>

        @php
            $user = auth()->user();
            $userName = $user?->name ?? 'User';

            $initials = collect(explode(' ', $userName))
                ->filter()
                ->take(2)
                ->map(fn ($word) => mb_substr($word, 0, 1))
                ->implode('');

            $initials = $initials ?: 'U';
        @endphp

        <div class="relative shrink-0" x-data="{ userMenuOpen: false }">
            <button
                type="button"
                @click="userMenuOpen = !userMenuOpen"
                class="flex max-w-[220px] items-center gap-2 rounded-full border border-slate-200 bg-white py-1.5 pl-1.5 pr-2 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 sm:gap-3 sm:pr-3"
            >
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold uppercase leading-none text-white ring-2 ring-white">
                    {{ $initials }}
                </span>

                {{-- Nama user disembunyikan di mobile dan layar kecil --}}
                <span class="hidden min-w-0 text-left xl:block">
                    <span class="block max-w-36 truncate text-sm font-semibold text-slate-800">
                        {{ $userName }}
                    </span>
                    <span class="block truncate text-xs capitalize text-slate-500">
                        {{ $user?->role?->name ?? '-' }}
                    </span>
                </span>

                <x-icon
                    name="chevron-down"
                    class="h-4 w-4 shrink-0 text-slate-400 transition"
                    x-bind:class="{ 'rotate-180': userMenuOpen }"
                />
            </button>

            <div
                x-show="userMenuOpen"
                x-transition.origin.top.right
                @click.outside="userMenuOpen = false"
                class="fixed left-4 right-4 top-20 z-50 rounded-2xl border border-slate-100 bg-white p-4 shadow-xl shadow-slate-900/10 sm:left-auto sm:right-6 sm:w-72 lg:right-8"
                style="display: none;"
            >
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-bold uppercase leading-none text-white">
                        {{ $initials }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-900">
                            {{ $userName }}
                        </p>
                        <p class="truncate text-xs capitalize text-slate-500">
                            {{ $user?->role?->name ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <a
                        href="{{ route('profile.show') }}"
                        class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        <span>Lihat Profil</span>
                        <x-icon name="arrow-right" class="h-4 w-4 text-slate-400" />
                    </a>
                </div>

                <div class="mt-3 border-t border-slate-100 pt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
                        >
                            <span>Keluar</span>
                            <x-icon name="log-out" class="h-4 w-4" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
