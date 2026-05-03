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

        <div class="flex items-center gap-4">
            <div class="hidden sm:block text-right">
                <div class="text-sm font-semibold text-gray-700">
                    {{ auth()->user()->name }}
                </div>
                <div class="text-xs text-gray-500 capitalize">
                    {{ auth()->user()->role?->name }}
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>