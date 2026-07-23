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
            class="pointer-events-auto fixed inset-y-0 left-0 flex h-dvh max-h-dvh w-full max-w-xs flex-col overflow-hidden bg-white shadow-xl shadow-slate-900/20"
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

            <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-4 pt-5 pb-24">
                @if(auth()->user()->isAdmin())
                <x-sidebar-link
                    :href="route('admin.dashboard')"
                    :active="request()->routeIs('admin.dashboard')"
                    @click.stop="sidebarOpen = false"
                    icon="layout-dashboard"
                >
                    Dashboard Admin
                </x-sidebar-link>

                {{-- DOKUMENTASI SIM TI --}}
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Dokumentasi SIM TI
                    </p>
                </div>

                <x-sidebar-group
                    title="Penetapan"
                    icon="file-check-2"
                    :active="request()->routeIs('documentation.penetapan.*')"
                >
                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'tupoksi-sim-ti'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'tupoksi-sim-ti'"
                        @click.stop="sidebarOpen = false"
                        icon="clipboard-list"
                    >
                        Tupoksi SIM TI
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'struktur-organisasi'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'struktur-organisasi'"
                        @click.stop="sidebarOpen = false"
                        icon="network"
                    >
                        Struktur Organisasi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sk-sdm-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sk-sdm-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="badge-check"
                    >
                        SK SDM Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'standar-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'standar-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="scale"
                    >
                        Standar Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sop-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sop-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="file-check-2"
                    >
                        SOP Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'formulir'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'formulir'"
                        @click.stop="sidebarOpen = false"
                        icon="file-output"
                    >
                        Formulir SIM TI
                    </x-sidebar-link>
                </x-sidebar-group>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Pelaksanaan"
                        icon="clipboard-check"
                        :active="
                            request()->routeIs('admin.unit-targets.*')
                            || request()->routeIs('admin.target-reports.*')
                            || request()->routeIs('reports.export.monthly')
                        "
                    >

                        <x-sidebar-link
                            :href="route('admin.unit-targets.index')"
                            :active="request()->routeIs('admin.unit-targets.*')"
                            @click.stop="sidebarOpen = false"
                            icon="target"
                        >
                            Target Tahunan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('admin.target-reports.index')"
                            :active="request()->routeIs('admin.target-reports.*')"
                            @click.stop="sidebarOpen = false"
                            icon="file-spreadsheet"
                        >
                            Laporan Capaian 3 Bulanan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('reports.export.monthly')"
                            :active="request()->routeIs('reports.export.monthly')"
                            @click.stop="sidebarOpen = false"
                            icon="bar-chart-3"
                        >
                            Rekap Laporan
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Evaluasi"
                        icon="search-check"
                        :active="request()->routeIs('documentation.evaluasi.*')"
                    >
                        <x-sidebar-link
                            :href="route('documentation.evaluasi.index')"
                            :active="request()->routeIs('documentation.evaluasi.*')"
                            @click.stop="sidebarOpen = false"
                            icon="clipboard-check"
                        >
                            Hasil Evaluasi
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <div class="mt-3">
                        <x-sidebar-group
                            title="Pengendalian"
                            icon="shield-check"
                            :active="request()->routeIs('documentation.control.*')"
                        >
                            <x-sidebar-link
                                :href="route('documentation.control.follow-ups.index')"
                                :active="request()->routeIs('documentation.control.follow-ups.*')"
                                @click.stop="sidebarOpen = false"
                                icon="list-checks"
                            >
                                Tindak Lanjut Evaluasi
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('documentation.control.letters.index')"
                                :active="request()->routeIs('documentation.control.letters.*')"
                                @click.stop="sidebarOpen = false"
                                icon="file-text"
                            >
                                Arsip Surat Pengendalian
                            </x-sidebar-link>
                        </x-sidebar-group>
                    </div>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Pengembangan"
                        icon="rocket"
                        :active="request()->routeIs('developments.*')"
                    >
                        <x-sidebar-link
                            :href="route('developments.plans.index')"
                            :active="request()->routeIs('developments.plans.*')"
                            @click.stop="sidebarOpen = false"
                            icon="rocket"
                        >
                            Rencana Pengembangan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('developments.documents.index')"
                            :active="request()->routeIs('developments.documents.*')"
                            @click.stop="sidebarOpen = false"
                            icon="file-text"
                        >
                            Dokumen Pengembangan
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Operasional SIM/TI"
                        icon="monitor-cog"
                        :active="request()->routeIs('operations.*')"
                    >
                        <x-sidebar-link
                            :href="route('operations.tickets.index')"
                            :active="request()->routeIs('operations.tickets.*')"
                            @click.stop="sidebarOpen = false"
                            icon="ticket-check"
                        >
                            Tiket Operasional
                        </x-sidebar-link>

                        <!-- <x-sidebar-link
                            :href="route('operations.forms.index')"
                            :active="request()->routeIs('operations.forms.*')"
                            @click.stop="sidebarOpen = false"
                            icon="clipboard-list"
                        >
                            Form Operasional
                        </x-sidebar-link> -->

                        <x-sidebar-link
                            :href="route('operations.documents.index')"
                            :active="request()->routeIs('operations.documents.*')"
                            @click.stop="sidebarOpen = false"
                            icon="folder-check"
                        >
                            Arsip Operasional
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>                

                {{-- ADMINISTRASI SISTEM --}}
                <div class="pt-5 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Administrasi Sistem
                    </p>
                </div>

                <x-sidebar-group
                    title="Master Data"
                    icon="database"
                    :active="
                        request()->routeIs('admin.master-data.unit.*')
                        || request()->routeIs('admin.master-data.pegawai.*')
                        || request()->routeIs('admin.admin.positions.*')
                        || request()->routeIs('admin.master-data.tupoksi.*')
                        || request()->routeIs('admin.master-data.duty-classifications.*')
                        || request()->routeIs('admin.master-data.report-template.*')
                        || request()->routeIs('admin.master-data.server.*')
                        || request()->routeIs('admin.master-data.aplikasi.*')
                    "
                >
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
                        :href="route('admin.positions.index')"
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
                        :href="route('admin.master-data.report-template.index')"
                        :active="request()->routeIs('admin.master-data.report-template.*')"
                        @click.stop="sidebarOpen = false"
                        icon="file-text"
                    >
                        Template Laporan
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
                </x-sidebar-group>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Manajemen User"
                        icon="users"
                        :active="request()->routeIs('admin.user-management.*')"
                    >
                        <x-sidebar-link
                            :href="route('admin.user-management.missing-accounts')"
                            :active="request()->routeIs('admin.user-management.missing-accounts')"
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

                        <x-sidebar-link
                            :href="route('admin.duty-delegations.index')"
                            :active="request()->routeIs('admin.admin.duty-delegations.*')"
                            @click.stop="sidebarOpen = false"
                            icon="git-branch"
                        >
                            Delegasi Tupoksi
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Audit Log"
                        icon="history"
                        :active="request()->routeIs('admin.activity-logs')"
                    >
                        <x-sidebar-link
                            :href="route('admin.activity-logs')"
                            :active="request()->routeIs('admin.activity-logs')"
                            @click.stop="sidebarOpen = false"
                            icon="history"
                        >
                            Log Aktivitas
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>
            @elseif(auth()->user()?->isKanit())
                <x-sidebar-link
                    :href="route('kanit.dashboard')"
                    :active="request()->routeIs('kanit.dashboard')"
                    @click.stop="sidebarOpen = false"
                    icon="layout-dashboard"
                >
                    Dashboard Kanit
                </x-sidebar-link>

                {{-- DOKUMENTASI SIM TI --}}
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Dokumentasi SIM TI
                    </p>
                </div>

                <x-sidebar-group
                    title="Penetapan"
                    icon="file-check-2"
                    :active="request()->routeIs('documentation.penetapan.*')"
                >
                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'tupoksi-sim-ti'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'tupoksi-sim-ti'"
                        @click.stop="sidebarOpen = false"
                        icon="clipboard-list"
                    >
                        Tupoksi SIM TI
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'struktur-organisasi'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'struktur-organisasi'"
                        @click.stop="sidebarOpen = false"
                        icon="network"
                    >
                        Struktur Organisasi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sk-sdm-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sk-sdm-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="badge-check"
                    >
                        SK SDM Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'standar-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'standar-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="scale"
                    >
                        Standar Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sop-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sop-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="file-check-2"
                    >
                        SOP Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'formulir'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'formulir'"
                        @click.stop="sidebarOpen = false"
                        icon="file-output"
                    >
                        Formulir SIM TI
                    </x-sidebar-link>
                </x-sidebar-group>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Pelaksanaan"
                        icon="clipboard-check"
                        :active="
                            request()->routeIs('kanit.unit-targets.*')
                            || request()->routeIs('kanit.target-reports.*')
                            || request()->routeIs('kanit.reports.*')
                            || request()->routeIs('reports.export.monthly')
                        "
                    >
                        <x-sidebar-link
                            :href="route('kanit.unit-targets.index')"
                            :active="request()->routeIs('kanit.unit-targets.*')"
                            @click.stop="sidebarOpen = false"
                            icon="target"
                        >
                            Target Tahunan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('kanit.target-reports.index')"
                            :active="request()->routeIs('kanit.target-reports.*')"
                            @click.stop="sidebarOpen = false"
                            icon="file-spreadsheet"
                        >
                            Laporan Capaian 3 Bulanan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('kanit.reports.monitoring')"
                            :active="request()->routeIs('kanit.reports.monitoring') || request()->routeIs('kanit.reports.detail')"
                            @click.stop="sidebarOpen = false"
                            icon="search"
                        >
                            Monitoring Laporan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('reports.export.monthly')"
                            :active="request()->routeIs('reports.export.monthly')"
                            @click.stop="sidebarOpen = false"
                            icon="bar-chart-3"
                        >
                            Rekap Laporan
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Evaluasi"
                        icon="search-check"
                        :active="request()->routeIs('documentation.evaluasi.*')"
                    >
                        <x-sidebar-link
                            :href="route('documentation.evaluasi.index')"
                            :active="request()->routeIs('documentation.evaluasi.*')"
                            @click.stop="sidebarOpen = false"
                            icon="clipboard-check"
                        >
                            Hasil Evaluasi
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <div class="mt-3">
                        <x-sidebar-group
                            title="Pengendalian"
                            icon="shield-check"
                            :active="request()->routeIs('documentation.control.*')"
                        >
                            <x-sidebar-link
                                :href="route('documentation.control.follow-ups.index')"
                                :active="request()->routeIs('documentation.control.follow-ups.*')"
                                @click.stop="sidebarOpen = false"
                                icon="list-checks"
                            >
                                Tindak Lanjut Evaluasi
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('documentation.control.letters.index')"
                                :active="request()->routeIs('documentation.control.letters.*')"
                                @click.stop="sidebarOpen = false"
                                icon="file-text"
                            >
                                Arsip Surat Pengendalian
                            </x-sidebar-link>
                        </x-sidebar-group>
                    </div>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Pengembangan"
                        icon="rocket"
                        :active="request()->routeIs('developments.*')"
                    >
                        <x-sidebar-link
                            :href="route('developments.plans.index')"
                            :active="request()->routeIs('developments.plans.*')"
                            @click.stop="sidebarOpen = false"
                            icon="rocket"
                        >
                            Rencana Pengembangan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('developments.documents.index')"
                            :active="request()->routeIs('developments.documents.*')"
                            @click.stop="sidebarOpen = false"
                            icon="file-text"
                        >
                            Dokumen Pengembangan
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Operasional SIM/TI"
                        icon="monitor-cog"
                        :active="request()->routeIs('operations.*')"
                    >
                        <x-sidebar-link
                            :href="route('operations.tickets.index')"
                            :active="request()->routeIs('operations.tickets.*')"
                            @click.stop="sidebarOpen = false"
                            icon="ticket-check"
                        >
                            Tiket Operasional
                        </x-sidebar-link>

                        <!-- <x-sidebar-link
                            :href="route('operations.forms.index')"
                            :active="request()->routeIs('operations.forms.*')"
                            @click.stop="sidebarOpen = false"
                            icon="clipboard-list"
                        >
                            Form Operasional
                        </x-sidebar-link> -->

                        <x-sidebar-link
                            :href="route('operations.documents.index')"
                            :active="request()->routeIs('operations.documents.*')"
                            @click.stop="sidebarOpen = false"
                            icon="folder-check"
                        >
                            Arsip Operasional
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                {{-- MANAJEMEN USER --}}
                <div class="pt-5 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Manajemen User
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('kanit.duty-delegations.index')"
                    :active="request()->routeIs('kanit.duty-delegations.*')"
                    @click.stop="sidebarOpen = false"
                    icon="git-branch"
                >
                    Delegasi Tupoksi
                </x-sidebar-link>

            @elseif(auth()->user()->canAccessEmployeeArea())
                <x-sidebar-link
                    :href="route('pegawai.dashboard')"
                    :active="request()->routeIs('pegawai.dashboard')"
                    @click.stop="sidebarOpen = false"
                    icon="layout-dashboard"
                >
                    Dashboard
                </x-sidebar-link>

                {{-- LAPORAN SAYA --}}
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Laporan Saya
                    </p>
                </div>

                <x-sidebar-group
                    title="Laporan Harian"
                    icon="clipboard-list"
                    :active="request()->routeIs('pegawai.reports.*')"
                >
                    <x-sidebar-link
                        :href="route('pegawai.reports.create')"
                        :active="request()->routeIs('pegawai.reports.create')"
                        @click.stop="sidebarOpen = false"
                        icon="plus-circle"
                    >
                        Input Laporan Harian
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('pegawai.reports.index')"
                        :active="request()->routeIs('pegawai.reports.index') || request()->routeIs('pegawai.reports.show') || request()->routeIs('pegawai.reports.edit')"
                        @click.stop="sidebarOpen = false"
                        icon="history"
                    >
                        Riwayat Laporan Saya
                    </x-sidebar-link>
                </x-sidebar-group>

                {{-- DOKUMENTASI SIM TI --}}
                <div class="pt-5 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Dokumentasi SIM TI
                    </p>
                </div>

                <x-sidebar-group
                    title="Penetapan"
                    icon="file-check-2"
                    :active="request()->routeIs('documentation.penetapan.*')"
                >
                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'tupoksi-sim-ti'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'tupoksi-sim-ti'"
                        @click.stop="sidebarOpen = false"
                        icon="clipboard-list"
                    >
                        Tupoksi SIM TI
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'struktur-organisasi'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'struktur-organisasi'"
                        @click.stop="sidebarOpen = false"
                        icon="network"
                    >
                        Struktur Organisasi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sk-sdm-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sk-sdm-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="badge-check"
                    >
                        SK SDM Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'standar-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'standar-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="scale"
                    >
                        Standar Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sop-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sop-unit'"
                        @click.stop="sidebarOpen = false"
                        icon="file-check-2"
                    >
                        SOP Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'formulir'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'formulir'"
                        @click.stop="sidebarOpen = false"
                        icon="file-output"
                    >
                        Formulir SIM TI
                    </x-sidebar-link>
                </x-sidebar-group>

                @if(auth()->user()->isGkm())
                    <div class="mt-3">
                        <x-sidebar-group
                            title="Pelaksanaan"
                            icon="clipboard-check"
                            :active="
                                request()->routeIs('kanit.unit-targets.*')
                                || request()->routeIs('kanit.target-reports.*')
                                || request()->routeIs('kanit.reports.*')
                                || request()->routeIs('reports.export.monthly')
                            "
                        >
                            <x-sidebar-link
                                :href="route('kanit.unit-targets.index')"
                                :active="request()->routeIs('kanit.unit-targets.*')"
                                @click.stop="sidebarOpen = false"
                                icon="target"
                            >
                                Target Tahunan
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('kanit.target-reports.index')"
                                :active="request()->routeIs('kanit.target-reports.*')"
                                @click.stop="sidebarOpen = false"
                                icon="file-spreadsheet"
                            >
                                Laporan Capaian 3 Bulanan
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('kanit.reports.monitoring')"
                                :active="request()->routeIs('kanit.reports.*')"
                                @click.stop="sidebarOpen = false"
                                icon="search"
                            >
                                Monitoring Laporan
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('reports.export.monthly')"
                                :active="request()->routeIs('reports.export.monthly')"
                                @click.stop="sidebarOpen = false"
                                icon="bar-chart-3"
                            >
                                Rekap Laporan
                            </x-sidebar-link>
                        </x-sidebar-group>
                    </div>
                @endif

                <div class="mt-3">
                    <x-sidebar-group
                        title="Evaluasi"
                        icon="search-check"
                        :active="request()->routeIs('documentation.evaluasi.*')"
                    >
                        <x-sidebar-link
                            :href="route('documentation.evaluasi.index')"
                            :active="request()->routeIs('documentation.evaluasi.*')"
                            @click.stop="sidebarOpen = false"
                            icon="clipboard-check"
                        >
                            Hasil Evaluasi
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <div class="mt-3">
                        <x-sidebar-group
                            title="Pengendalian"
                            icon="shield-check"
                            :active="request()->routeIs('documentation.control.*')"
                        >
                            <x-sidebar-link
                                :href="route('documentation.control.follow-ups.index')"
                                :active="request()->routeIs('documentation.control.follow-ups.*')"
                                @click.stop="sidebarOpen = false"
                                icon="list-checks"
                            >
                                Tindak Lanjut Evaluasi
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('documentation.control.letters.index')"
                                :active="request()->routeIs('documentation.control.letters.*')"
                                @click.stop="sidebarOpen = false"
                                icon="file-text"
                            >
                                Arsip Surat Pengendalian
                            </x-sidebar-link>
                        </x-sidebar-group>
                    </div>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Pengembangan"
                        icon="rocket"
                        :active="request()->routeIs('developments.*')"
                    >
                        <x-sidebar-link
                            :href="route('developments.plans.index')"
                            :active="request()->routeIs('developments.plans.*')"
                            @click.stop="sidebarOpen = false"
                            icon="rocket"
                        >
                            Rencana Pengembangan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('developments.documents.index')"
                            :active="request()->routeIs('developments.documents.*')"
                            @click.stop="sidebarOpen = false"
                            icon="file-text"
                        >
                            Dokumen Pengembangan
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                    <x-sidebar-group
                        title="Operasional SIM/TI"
                        icon="monitor-cog"
                        :active="request()->routeIs('operations.*')"
                    >
                        <x-sidebar-link
                            :href="route('operations.tickets.index')"
                            :active="request()->routeIs('operations.tickets.*')"
                            @click.stop="sidebarOpen = false"
                            icon="ticket-check"
                        >
                            Tiket Operasional
                        </x-sidebar-link>

                        <!-- <x-sidebar-link
                            :href="route('operations.forms.index')"
                            :active="request()->routeIs('operations.forms.*')"
                            @click.stop="sidebarOpen = false"
                            icon="clipboard-list"
                        >
                            Form Operasional
                        </x-sidebar-link> -->

                        <x-sidebar-link
                            :href="route('operations.documents.index')"
                            :active="request()->routeIs('operations.documents.*')"
                            @click.stop="sidebarOpen = false"
                            icon="folder-check"
                        >
                            Arsip Operasional
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

            @endif
            </nav>
        </div>
    </div>
</div>