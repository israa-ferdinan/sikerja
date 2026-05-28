<header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">

    {{-- Mobile menu button --}}
    <button type="button"
            class="inline-flex items-center justify-center rounded-md p-2 text-gray-700 hover:bg-gray-100 lg:hidden"
            @click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>
        <span class="text-2xl leading-none">☰</span>
    </button>

    <div class="h-6 w-px bg-gray-200 lg:hidden"></div>

    <div class="flex flex-1 items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-700">
                Aplikasi Laporan Kerja Kantor
            </h2>
            <p class="text-xs text-gray-500">
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
        @endphp

        <div class="relative" x-data="{ userMenuOpen: false }">
            <button
                type="button"
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-3 rounded-full border border-gray-200 bg-white py-1.5 pl-1.5 pr-3 shadow-sm transition hover:bg-gray-50"
            >
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-xs font-bold uppercase text-white ring-2 ring-white">
                    {{ $initials }}
                </span>

                <span class="hidden text-left sm:block">
                    <span class="block max-w-40 truncate text-sm font-semibold text-gray-700">
                        {{ $userName }}
                    </span>
                    <span class="block text-xs capitalize text-gray-500">
                        {{ $user?->role?->name ?? '-' }}
                    </span>
                </span>

                <svg class="h-4 w-4 text-gray-400 transition"
                     :class="{ 'rotate-180': userMenuOpen }"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div
                x-show="userMenuOpen"
                x-transition.origin.top.right
                @click.outside="userMenuOpen = false"
                class="absolute right-0 z-50 mt-3 w-72 rounded-2xl border border-gray-100 bg-white p-4 shadow-xl"
                style="display: none;"
            >
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-800 text-sm font-bold uppercase text-white">
                        {{ $initials }}
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">
                            {{ $userName }}
                        </p>
                        <p class="text-xs capitalize text-gray-500">
                            {{ $user?->role?->name ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <a
                        href="{{ route('profile.show') }}"
                        class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        <span>Lihat Profil</span>
                        <span class="text-gray-400">→</span>
                    </a>
                </div>

                <div class="mt-3 border-t border-gray-100 pt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
                        >
                            <span>Keluar</span>
                            <span>↗</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>