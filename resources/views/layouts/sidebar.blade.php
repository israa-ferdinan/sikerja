<aside class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col bg-white border-r border-gray-200">
    <div class="flex min-h-0 flex-1 flex-col">
        <div class="flex h-16 items-center border-b px-6">
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
                >
                    Dashboard Admin
                </x-sidebar-link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Master Data
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('admin.master-data.unit.index')"
                    :active="request()->routeIs('admin.master-data.unit.*')"
                >
                    Master Unit
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.pegawai.index')"
                    :active="request()->routeIs('admin.master-data.pegawai.*')"
                >
                    Master Pegawai
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.admin.positions.index')"
                    :active="request()->routeIs('admin.admin.positions.*')"
                >
                    Master Jabatan
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.user-management.missing-accounts')"
                    :active="request()->routeIs('admin.user-management.missing-accounts.*')"
                >
                    Aktivasi Akun
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.user-management.users.index')"
                    :active="request()->routeIs('admin.user-management.users.index.*')"
                >
                    Managemen User
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.tupoksi.index')"
                    :active="request()->routeIs('admin.master-data.tupoksi.*')"
                >
                    Master Tupoksi
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.server.index')"
                    :active="request()->routeIs('admin.master-data.server.*')"
                >
                    Master Server
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.aplikasi.index')"
                    :active="request()->routeIs('admin.master-data.aplikasi.*')"
                >
                    Master Aplikasi
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.report-template.index')"
                    :active="request()->routeIs('admin.master-data.report-template.*')"
                >
                    Template Laporan
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('reports.export.monthly')"
                    :active="request()->routeIs('reports.export.monthly.*')"
                >
                    Rekap & Export
                </x-sidebar-link>
                
            @endif

            @if(auth()->user()?->isKanit())
                <x-sidebar-link
                    :href="route('kanit.dashboard')"
                    :active="request()->routeIs('kanit.dashboard')"
                >
                    Dashboard Kanit
                </x-sidebar-link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Laporan Unit
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('kanit.reports.monitoring')"
                    :active="request()->routeIs('kanit.reports.*')"
                >
                    Monitoring Laporan Unit
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('reports.export.monthly')"
                    :active="request()->routeIs('reports.export.monthly.*')"
                >
                    Rekap & Export
                </x-sidebar-link>

            @endif

            @if(auth()->user()->isPegawai())
                <x-sidebar-link
                    :href="route('pegawai.dashboard')"
                    :active="request()->routeIs('pegawai.dashboard')"
                >
                    Dashboard Pegawai
                </x-sidebar-link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Laporan Saya
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('pegawai.reports.create')"
                    :active="request()->routeIs('pegawai.reports.create')"
                >
                    Input Laporan
                </x-sidebar-link>

                <x-sidebar-link
                    :href="route('pegawai.reports.index')"
                    :active="request()->routeIs('pegawai.reports.index')"
                >
                    Riwayat Laporan Saya
                </x-sidebar-link>
            @endif
        </nav>
    </div>
</aside>