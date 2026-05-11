<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\MasterData\Pegawai\Index as PegawaiIndex;
use App\Livewire\Admin\MasterData\Tupoksi\Index as TupoksiIndex;
use App\Livewire\Admin\MasterData\Unit\Index as UnitIndex;
use App\Livewire\Admin\MasterData\Server\Index as ServerIndex;
use App\Livewire\Admin\MasterData\Aplikasi\Index as AplikasiIndex;
use App\Livewire\Admin\MasterData\ReportTemplate\Index as ReportTemplateIndex;

use App\Livewire\Kanit\Dashboard as KanitDashboard;
use App\Livewire\Kanit\ReportMonitoring;

use App\Livewire\Pegawai\Dashboard as PegawaiDashboard;

use App\Livewire\Reports\CreateDailyReport;
use App\Livewire\Reports\MyDailyReports;
use App\Livewire\Reports\ShowDailyReport;
use App\Livewire\Reports\EditDailyReport;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role?->name;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kanit' => redirect()->route('kanit.dashboard'),
            'pegawai' => redirect()->route('pegawai.dashboard'),
            default => abort(403, 'Role user belum dikenali.'),
        };
    })->name('dashboard');

    Route::get('/reports/create', CreateDailyReport::class)->name('reports.create');

Route::middleware(['role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboard::class)
            ->name('dashboard');

        Route::get('/master-data/unit', UnitIndex::class)
            ->name('master-data.unit.index');

        Route::get('/master-data/pegawai', PegawaiIndex::class)
            ->name('master-data.pegawai.index');

        Route::get('/master-data/tupoksi', TupoksiIndex::class)
            ->name('master-data.tupoksi.index');

        Route::get('/master-data/server', ServerIndex::class)
            ->name('master-data.server.index');

        Route::get('/master-data/aplikasi', AplikasiIndex::class)
            ->name('master-data.aplikasi.index');

        Route::get('/master-data/template-laporan', ReportTemplateIndex::class)
            ->name('master-data.report-template.index');            
    }); 

Route::middleware(['role:kanit'])->prefix('kanit')->name('kanit.')->group(function () {
        Route::get('/dashboard', KanitDashboard::class)->name('dashboard');
        Route::get('/monitoring-laporan', ReportMonitoring::class)->name('reports.monitoring');
    });

Route::middleware(['role:pegawai'])
    ->prefix('pegawai')
    ->name('pegawai.')
    ->group(function () {
        Route::get('/dashboard', PegawaiDashboard::class)
            ->name('dashboard');
        Route::get('/reports/create', CreateDailyReport::class)
            ->name('reports.create');
        Route::get('/reports', MyDailyReports::class)
            ->name('reports.index');
        Route::get('/reports/{report}', ShowDailyReport::class)
            ->name('reports.show');
        Route::get('/reports/{report}/edit', EditDailyReport::class)
            ->name('reports.edit');
    });

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

require __DIR__.'/auth.php';