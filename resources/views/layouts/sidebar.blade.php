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
                        icon="clipboard-list"
                    >
                        Tupoksi SIM TI
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'struktur-organisasi'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'struktur-organisasi'"
                        icon="network"
                    >
                        Struktur Organisasi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sk-sdm-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sk-sdm-unit'"
                        icon="badge-check"
                    >
                        SK SDM Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'standar-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'standar-unit'"
                        icon="scale"
                    >
                        Standar Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sop-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sop-unit'"
                        icon="file-check-2"
                    >
                        SOP Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'formulir'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'formulir'"
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
                            icon="target"
                        >
                            Target Tahunan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('admin.target-reports.index')"
                            :active="request()->routeIs('admin.target-reports.*')"
                            icon="file-spreadsheet"
                        >
                            Laporan Capaian 3 Bulanan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('reports.export.monthly')"
                            :active="request()->routeIs('reports.export.monthly')"
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
                            icon="clipboard-check"
                        >
                            Hasil Evaluasi
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                        <x-sidebar-group
                            title="Pengendalian"
                            icon="shield-check"
                            :active="request()->routeIs('documentation.control.*')"
                        >
                            <x-sidebar-link
                                :href="route('documentation.control.follow-ups.index')"
                                :active="request()->routeIs('documentation.control.follow-ups.*')"
                                icon="list-checks"
                            >
                                Tindak Lanjut Evaluasi
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('documentation.control.letters.index')"
                                :active="request()->routeIs('documentation.control.letters.*')"
                                icon="file-text"
                            >
                                Arsip Surat Pengendalian
                            </x-sidebar-link>
                        </x-sidebar-group>
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
                            icon="rocket"
                        >
                            Rencana Pengembangan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('developments.documents.index')"
                            :active="request()->routeIs('developments.documents.*')"
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
                            icon="ticket-check"
                        >
                            Tiket Operasional
                        </x-sidebar-link>

                        <!-- <x-sidebar-link
                            :href="route('operations.forms.index')"
                            :active="request()->routeIs('operations.forms.*')"
                            icon="clipboard-list"
                        >
                            Form Operasional
                        </x-sidebar-link> -->

                        <x-sidebar-link
                            :href="route('operations.documents.index')"
                            :active="request()->routeIs('operations.documents.*')"
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
                        || request()->routeIs('admin.positions.*')
                        || request()->routeIs('admin.master-data.tupoksi.*')
                        || request()->routeIs('admin.master-data.duty-classifications.*')
                        || request()->routeIs('admin.master-data.server.*')
                        || request()->routeIs('admin.master-data.aplikasi.*')
                        || request()->routeIs('admin.master-data.report-template.*')
                    "
                >
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
                        :href="route('admin.positions.index')"
                        :active="request()->routeIs('admin.positions.*')"
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
                        :active="request()->routeIs('admin.master-data.duty-classifications.*')"
                        icon="settings"
                    >
                        Klasifikasi Tupoksi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('admin.master-data.report-template.index')"
                        :active="request()->routeIs('admin.master-data.report-template.*')"
                        icon="file-text"
                    >
                        Template Laporan
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
                            icon="user-check"
                        >
                            Aktivasi Akun
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('admin.user-management.users.index')"
                            :active="request()->routeIs('admin.user-management.users.*')"
                            icon="users"
                        >
                            Manajemen User
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('admin.duty-delegations.index')"
                            :active="request()->routeIs('admin.duty-delegations.*')"
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
                            icon="history"
                        >
                            Log Aktivitas
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>
            @endif

            @if(auth()->user()?->isKanit())
                <x-sidebar-link
                    :href="route('kanit.dashboard')"
                    :active="request()->routeIs('kanit.dashboard')"
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
                        icon="clipboard-list"
                    >
                        Tupoksi SIM TI
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'struktur-organisasi'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'struktur-organisasi'"
                        icon="network"
                    >
                        Struktur Organisasi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sk-sdm-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sk-sdm-unit'"
                        icon="badge-check"
                    >
                        SK SDM Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'standar-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'standar-unit'"
                        icon="scale"
                    >
                        Standar Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sop-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sop-unit'"
                        icon="file-check-2"
                    >
                        SOP Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'formulir'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'formulir'"
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
                            icon="target"
                        >
                            Target Tahunan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('kanit.target-reports.index')"
                            :active="request()->routeIs('kanit.target-reports.*')"
                            icon="file-spreadsheet"
                        >
                            Laporan Capaian 3 Bulanan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('kanit.reports.monitoring')"
                            :active="request()->routeIs('kanit.reports.*')"
                            icon="search"
                        >
                            Monitoring Laporan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('reports.export.monthly')"
                            :active="request()->routeIs('reports.export.monthly')"
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
                            icon="clipboard-check"
                        >
                            Hasil Evaluasi
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                        <x-sidebar-group
                            title="Pengendalian"
                            icon="shield-check"
                            :active="request()->routeIs('documentation.control.*')"
                        >
                            <x-sidebar-link
                                :href="route('documentation.control.follow-ups.index')"
                                :active="request()->routeIs('documentation.control.follow-ups.*')"
                                icon="list-checks"
                            >
                                Tindak Lanjut Evaluasi
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('documentation.control.letters.index')"
                                :active="request()->routeIs('documentation.control.letters.*')"
                                icon="file-text"
                            >
                                Arsip Surat Pengendalian
                            </x-sidebar-link>
                        </x-sidebar-group>
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
                            icon="rocket"
                        >
                            Rencana Pengembangan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('developments.documents.index')"
                            :active="request()->routeIs('developments.documents.*')"
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
                            icon="ticket-check"
                        >
                            Tiket Operasional
                        </x-sidebar-link>

                        <!-- <x-sidebar-link
                            :href="route('operations.forms.index')"
                            :active="request()->routeIs('operations.forms.*')"
                            icon="clipboard-list"
                        >
                            Form Operasional
                        </x-sidebar-link> -->

                        <x-sidebar-link
                            :href="route('operations.documents.index')"
                            :active="request()->routeIs('operations.documents.*')"
                            icon="folder-check"
                        >
                            Arsip Operasional
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                {{-- MANAJEMEN USER --}}
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Manajemen User
                    </p>
                </div>

                <x-sidebar-link
                    :href="route('kanit.duty-delegations.index')"
                    :active="request()->routeIs('kanit.duty-delegations.*')"
                    icon="git-branch"
                >
                    Delegasi Tupoksi
                </x-sidebar-link>
            @endif

            @if(auth()->user()->canAccessEmployeeArea())
                <x-sidebar-link
                    :href="route('pegawai.dashboard')"
                    :active="request()->routeIs('pegawai.dashboard')"
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
                        icon="plus-circle"
                    >
                        Input Laporan Harian
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('pegawai.reports.index')"
                        :active="request()->routeIs('pegawai.reports.index') || request()->routeIs('pegawai.reports.show') || request()->routeIs('pegawai.reports.edit')"
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
                        icon="clipboard-list"
                    >
                        Tupoksi SIM TI
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'struktur-organisasi'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'struktur-organisasi'"
                        icon="network"
                    >
                        Struktur Organisasi
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sk-sdm-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sk-sdm-unit'"
                        icon="badge-check"
                    >
                        SK SDM Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'standar-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'standar-unit'"
                        icon="scale"
                    >
                        Standar Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'sop-unit'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'sop-unit'"
                        icon="file-check-2"
                    >
                        SOP Unit
                    </x-sidebar-link>

                    <x-sidebar-link
                        :href="route('documentation.penetapan.index', ['category' => 'formulir'])"
                        :active="request()->routeIs('documentation.penetapan.*') && request('category') === 'formulir'"
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
                                icon="target"
                            >
                                Target Tahunan
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('kanit.target-reports.index')"
                                :active="request()->routeIs('kanit.target-reports.*')"
                                icon="file-spreadsheet"
                            >
                                Laporan Capaian 3 Bulanan
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('kanit.reports.monitoring')"
                                :active="request()->routeIs('kanit.reports.*')"
                                icon="search"
                            >
                                Monitoring Laporan
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('reports.export.monthly')"
                                :active="request()->routeIs('reports.export.monthly')"
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
                            icon="clipboard-check"
                        >
                            Hasil Evaluasi
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

                <div class="mt-3">
                        <x-sidebar-group
                            title="Pengendalian"
                            icon="shield-check"
                            :active="request()->routeIs('documentation.control.*')"
                        >
                            <x-sidebar-link
                                :href="route('documentation.control.follow-ups.index')"
                                :active="request()->routeIs('documentation.control.follow-ups.*')"
                                icon="list-checks"
                            >
                                Tindak Lanjut Evaluasi
                            </x-sidebar-link>

                            <x-sidebar-link
                                :href="route('documentation.control.letters.index')"
                                :active="request()->routeIs('documentation.control.letters.*')"
                                icon="file-text"
                            >
                                Arsip Surat Pengendalian
                            </x-sidebar-link>
                        </x-sidebar-group>
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
                            icon="rocket"
                        >
                            Rencana Pengembangan
                        </x-sidebar-link>

                        <x-sidebar-link
                            :href="route('developments.documents.index')"
                            :active="request()->routeIs('developments.documents.*')"
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
                            icon="ticket-check"
                        >
                            Tiket Operasional
                        </x-sidebar-link>

                        <!-- <x-sidebar-link
                            :href="route('operations.forms.index')"
                            :active="request()->routeIs('operations.forms.*')"
                            icon="clipboard-list"
                        >
                            Form Operasional
                        </x-sidebar-link> -->

                        <x-sidebar-link
                            :href="route('operations.documents.index')"
                            :active="request()->routeIs('operations.documents.*')"
                            icon="folder-check"
                        >
                            Arsip Operasional
                        </x-sidebar-link>
                    </x-sidebar-group>
                </div>

            @endif
        </nav>
    </div>
</aside>