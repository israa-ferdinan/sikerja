<div x-cloak
     x-show="sidebarOpen"
     class="relative z-50 lg:hidden"
     aria-modal="true">

    {{-- Overlay: klik area luar untuk tutup --}}
    <div x-show="sidebarOpen"
         x-transition.opacity
         class="fixed inset-0 bg-gray-900/70"
         @click="sidebarOpen = false">
    </div>

    <div class="fixed inset-0 flex">
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative flex w-full max-w-xs flex-1">

            <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                <button type="button"
                        class="-m-2.5 p-2.5 text-white"
                        @click="sidebarOpen = false">
                    <span class="sr-only">Close sidebar</span>
                    <span class="text-2xl">✕</span>
                </button>
            </div>

            <div class="flex grow flex-col overflow-y-auto bg-white pb-4">
                <div class="flex h-16 shrink-0 items-center border-b px-6">
                    <div>
                        <h1 class="text-base font-bold text-gray-800">
                            Laporan Kerja
                        </h1>
                        <p class="text-xs text-gray-500 capitalize">
                            {{ auth()->user()->role?->name }}
                        </p>
                    </div>
                </div>

                <nav class="flex-1 space-y-1 px-4 py-5">

                    @if(auth()->user()->isAdmin())
                        <x-sidebar-link
                            :href="route('admin.dashboard')"
                            :active="request()->routeIs('admin.dashboard')"
                            @click="sidebarOpen = false">
                            Dashboard Admin
                        </x-sidebar-link>

                        <div class="pt-4 pb-1">
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Master Data
                            </p>
                        </div>

                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Master Unit</x-sidebar-link>
                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Master Pegawai</x-sidebar-link>
                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Master Tupoksi</x-sidebar-link>
                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Master Server</x-sidebar-link>
                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Master Aplikasi</x-sidebar-link>
                    @endif

                    @if(auth()->user()->isKanit())
                        <x-sidebar-link
                            :href="route('kanit.dashboard')"
                            :active="request()->routeIs('kanit.dashboard')"
                            @click="sidebarOpen = false">
                            Dashboard Kanit
                        </x-sidebar-link>

                        <div class="pt-4 pb-1">
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Laporan
                            </p>
                        </div>

                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Laporan Unit</x-sidebar-link>
                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Rekap Bulanan</x-sidebar-link>
                    @endif

                    @if(auth()->user()->isPegawai())
                        <x-sidebar-link
                            :href="route('pegawai.dashboard')"
                            :active="request()->routeIs('pegawai.dashboard')"
                            @click="sidebarOpen = false">
                            Dashboard Pegawai
                        </x-sidebar-link>

                        <div class="pt-4 pb-1">
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Laporan Saya
                            </p>
                        </div>

                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Input Laporan</x-sidebar-link>
                        <x-sidebar-link href="#" :active="false" @click="sidebarOpen = false">Riwayat Laporan Saya</x-sidebar-link>
                    @endif

                </nav>
            </div>
        </div>
    </div>
</div>