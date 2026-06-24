<div
    x-cloak
    x-show="sidebarOpen"
    class="relative z-50 lg:hidden"
    role="dialog"
    aria-modal="true"
>
    {{-- Overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 bg-slate-900/70"
        @click="sidebarOpen = false"
    ></div>

    {{-- Sidebar panel --}}
    <div class="fixed inset-0 z-50 pointer-events-none">
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="pointer-events-auto fixed left-0 top-0 bottom-0 flex w-full max-w-xs flex-col overflow-hidden bg-white shadow-xl shadow-slate-900/20"
            style="height: 100vh; max-height: 100vh;"
        >
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-100 px-6">
                <div>
                    <h1 class="text-base font-bold text-slate-900">
                        Laporan Kerja
                    </h1>
                    <p class="text-xs text-slate-500 capitalize">
                        {{ auth()->user()->role?->name }}
                    </p>
                </div>

                <button
                    type="button"
                    class="-mr-2 rounded-lg p-2 text-slate-600 hover:bg-slate-100"
                    @click.stop="sidebarOpen = false"
                >
                    <span class="sr-only">Close sidebar</span>
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <nav
                class="space-y-1 overflow-y-auto overscroll-contain px-4 pt-5 pb-16"
                style="height: calc(100vh - 4rem); max-height: calc(100vh - 4rem);"
            >
                @if(auth()->user()->isAdmin())
                    <x-sidebar-link
                        :href="route('admin.dashboard')"
                        :active="request()->routeIs('admin.dashboard')"
                        @click.stop="sidebarOpen = false"
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
                        @click.stop="sidebarOpen = false"
                        icon="building-2"
                    >
                        Master Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.master-data.pegawai.index')"
                        :active="request()->routeIs('admin.master-data.pegawai.*')"
                        @click.stop="sidebarOpen = false"
                        icon="users"
                    >
                        Master Pegawai
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.admin.positions.index')"
                        :active="request()->routeIs('admin.admin.positions.*')"
                        @click.stop="sidebarOpen = false"
                        icon="briefcase"
                    >
                        Master Jabatan
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.master-data.tupoksi.index')"
                        :active="request()->routeIs('admin.master-data.tupoksi.*')"
                        @click.stop="sidebarOpen = false"
                        icon="clipboard-list"
                    >
                        Master Tupoksi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.master-data.duty-classifications.index')"
                        :active="request()->routeIs('admin.master-data.duty-classifications.*')"
                        @click.stop="sidebarOpen = false"
                        icon="settings"
                    >
                        Klasifikasi Tupoksi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.master-data.server.index')"
                        :active="request()->routeIs('admin.master-data.server.*')"
                        @click.stop="sidebarOpen = false"
                        icon="server"
                    >
                        Master Server
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.master-data.aplikasi.index')"
                        :active="request()->routeIs('admin.master-data.aplikasi.*')"
                        @click.stop="sidebarOpen = false"
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
                        @click.stop="sidebarOpen = false"
                        icon="user-check"
                    >
                        Aktivasi Akun
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.user-management.users.index')"
                        :active="request()->routeIs('admin.user-management.users.*')"
                        @click.stop="sidebarOpen = false"
                        icon="users"
                    >
                        Manajemen User
                    </x-sidebar-link>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Tupoksi
                        </p>
                    </div>

                    <x-sidebar-link
                        :href="route('admin.master-data.report-template.index')"
                        :active="request()->routeIs('admin.master-data.report-template.*')"
                        @click.stop="sidebarOpen = false"
                        icon="file-text"
                    >
                        Template Laporan
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('reports.export.monthly')"
                        :active="request()->routeIs('reports.export.monthly.*')"
                        @click.stop="sidebarOpen = false"
                        icon="file-spreadsheet"
                    >
                        Rekap & Export
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.admin.duty-delegations.index')"
                        :active="request()->routeIs('admin.admin.duty-delegations.*')"
                        @click.stop="sidebarOpen = false"
                        icon="users"
                    >
                        Delegasi Tupoksi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.unit-targets.index')"
                        :active="request()->routeIs('admin.unit-targets.*')"
                        @click.stop="sidebarOpen = false"
                        icon="target"
                    >
                        Target Unit
                    </x-sidebar-link>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Log
                        </p>
                    </div>

                    <x-sidebar-link
                        :href="route('admin.activity-logs')"
                        :active="request()->routeIs('admin.activity-logs.*')"
                        @click.stop="sidebarOpen = false"
                        icon="history"
                    >
                        Log Aktivitas
                    </x-sidebar-link>

                @elseif(auth()->user()?->isKanit())
                    <x-sidebar-link
                        :href="route('kanit.dashboard')"
                        :active="request()->routeIs('kanit.dashboard')"
                        @click.stop="sidebarOpen = false"
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
                        @click.stop="sidebarOpen = false"
                        icon="file-text"
                    >
                        Monitoring Laporan Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('kanit.duty-delegations.index')"
                        :active="request()->routeIs('kanit.duty-delegations.*')"
                        @click.stop="sidebarOpen = false"
                        icon="users"
                    >
                        Delegasi Tupoksi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('kanit.unit-targets.index')"
                        :active="request()->routeIs('kanit.unit-targets.*')"
                        @click.stop="sidebarOpen = false"
                        icon="target"
                    >
                        Target Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('reports.export.monthly')"
                        :active="request()->routeIs('reports.export.monthly.*')"
                        @click.stop="sidebarOpen = false"
                        icon="file-spreadsheet"
                    >
                        Rekap & Export
                    </x-sidebar-link>

                @elseif(auth()->user()->isPegawai())
                    <x-sidebar-link
                        :href="route('pegawai.dashboard')"
                        :active="request()->routeIs('pegawai.dashboard')"
                        @click.stop="sidebarOpen = false"
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
                        @click.stop="sidebarOpen = false"
                        icon="file-text"
                    >
                        Input Laporan
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('pegawai.reports.index')"
                        :active="request()->routeIs('pegawai.reports.index')"
                        @click.stop="sidebarOpen = false"
                        icon="history"
                    >
                        Riwayat Laporan Saya
                    </x-sidebar-link>
                @endif
            </nav>
        </div>
    </div>
</div>