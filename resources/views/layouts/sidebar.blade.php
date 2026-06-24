<aside class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col overflow-hidden bg-white/90 border-r border-slate-200 backdrop-blur-xl">
   <div class="flex min-h-0 h-full flex-1 flex-col">
        <div class="flex h-16 items-center border-b border-slate-100 px-6">
            <div>
                <h1 class="text-base font-bold text-slate-900">
                    Laporan Kerja
                </h1>
                <p class="text-xs text-slate-500 capitalize">
                    {{ auth()->user()->role?->name }}
                </p>
            </div>
        </div>

        <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-4 py-5">

            @if(auth()->user()->isAdmin())
                <x-sidebar-link
                    :href="route('admin.dashboard')"
                    :active="request()->routeIs('admin.dashboard')"
                        icon="layout-dashboard"
                >
                    Dashboard Admin
                    </x-sidebar-link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Master Data
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('admin.master-data.unit.index')"
                    :active="request()->routeIs('admin.master-data.unit.*')"
                        icon="building-2"
                >
                    Master Unit
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.pegawai.index')"
                    :active="request()->routeIs('admin.master-data.pegawai.*')"
                        icon="users"
                >
                    Master Pegawai
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.admin.positions.index')"
                    :active="request()->routeIs('admin.admin.positions.*')"
                        icon="briefcase"
                >
                    Master Jabatan
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.tupoksi.index')"
                    :active="request()->routeIs('admin.master-data.tupoksi.*')"
                        icon="clipboard-list"
                >
                    Master Tupoksi
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.duty-classifications.index')"
                    :active="request()->routeIs('admin.master-data.duty-classifications.index.*')"
                        icon="settings"
                >
                    Klasifikasi Tupoksi
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.server.index')"
                    :active="request()->routeIs('admin.master-data.server.*')"
                        icon="server"
                >
                    Master Server
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.master-data.aplikasi.index')"
                    :active="request()->routeIs('admin.master-data.aplikasi.*')"
                        icon="app-window"
                >
                    Master Aplikasi
                    </x-sidebar-link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        User
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('admin.user-management.missing-accounts')"
                    :active="request()->routeIs('admin.user-management.missing-accounts.*')"
                        icon="user-check"
                >
                    Aktivasi Akun
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.user-management.users.index')"
                    :active="request()->routeIs('admin.user-management.users.index.*')"
                        icon="users"
                >
                    Managemen User
                    </x-sidebar-link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Tupoksi
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('admin.master-data.report-template.index')"
                    :active="request()->routeIs('admin.master-data.report-template.*')"
                        icon="file-text"
                >
                    Template Laporan
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('reports.export.monthly')"
                    :active="request()->routeIs('reports.export.monthly.*')"
                        icon="file-spreadsheet"
                >
                    Rekap & Export
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.admin.duty-delegations.index')"
                    :active="request()->routeIs('admin.admin.duty-delegations.index.*')"
                        icon="users"
                >
                    Delegasi Tupoksi
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.unit-targets.index')"
                    :active="request()->routeIs('admin.unit-targets.*')"
                        icon="target"
                >
                    Target Unit
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('admin.target-reports.index')"
                    :active="request()->routeIs('admin.target-reports.*')"
                        icon="file-spreadsheet"
                >
                    Laporan Capaian Target
                    </x-sidebar-link>


                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Log
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('admin.activity-logs')"
                    :active="request()->routeIs('admin.activity-logs.*')"
                        icon="history"
                >
                    Log Aktivitas
                    </x-sidebar-link>
                
            @endif

            @if(auth()->user()?->isKanit())
                <x-sidebar-link
                    :href="route('kanit.dashboard')"
                    :active="request()->routeIs('kanit.dashboard')"
                        icon="layout-dashboard"
                >
                    Dashboard Kanit
                    </x-sidebar-link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Laporan Unit
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('kanit.reports.monitoring')"
                    :active="request()->routeIs('kanit.reports.*')"
                        icon="file-text"
                >
                    Monitoring Laporan Unit
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('kanit.duty-delegations.index')"
                    :active="request()->routeIs('kanit.duty-delegations.index.*')"
                        icon="users"
                >
                    Delegasi Tupoksi
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('kanit.unit-targets.index')"
                    :active="request()->routeIs('kanit.unit-targets.*')"
                        icon="target"
                >
                    Target Unit
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('kanit.target-reports.index')"
                    :active="request()->routeIs('kanit.target-reports.*')"
                        icon="file-spreadsheet"
                >
                    Laporan Capaian Target
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('reports.export.monthly')"
                    :active="request()->routeIs('reports.export.monthly.*')"
                        icon="file-spreadsheet"
                >
                    Rekap & Export
                    </x-sidebar-link>
            @endif

            @if(auth()->user()->isPegawai())
                <x-sidebar-link
                    :href="route('pegawai.dashboard')"
                    :active="request()->routeIs('pegawai.dashboard')"
                        icon="layout-dashboard"
                >
                    Dashboard Pegawai
                    </x-sidebar-link>

                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Laporan Saya
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('pegawai.reports.create')"
                    :active="request()->routeIs('pegawai.reports.create')"
                        icon="file-text"
                >
                    Input Laporan
                    </x-sidebar-link>

                <x-sidebar-link
                    :href="route('pegawai.reports.index')"
                    :active="request()->routeIs('pegawai.reports.index')"
                        icon="history"
                >
                    Riwayat Laporan Saya
                    </x-sidebar-link>
            @endif
        </nav>
    </div>
</aside>