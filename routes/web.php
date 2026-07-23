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
use App\Livewire\Admin\Positions\Index as PositionIndex;
use App\Livewire\Admin\UserManagement\MissingAccounts;
use App\Livewire\Admin\UserManagement\Users\Index as UserManagementIndex;
use App\Livewire\Admin\MasterData\Pegawai\ManageDuties;
use App\Livewire\Admin\DutyDelegations\Index as DutyDelegationIndex;
use App\Livewire\Admin\ActivityLogs\Index as ActivityLogIndex;
use App\Livewire\Admin\MasterData\DutyClassifications\Index as DutyClassificationIndex;
use App\Livewire\Admin\UnitTarget\Index as UnitTargetIndex;
use App\Livewire\Admin\TargetReport\Index as TargetReportIndex;

use App\Livewire\Kanit\Dashboard as KanitDashboard;
use App\Livewire\Kanit\ReportMonitoring;
use App\Livewire\Kanit\ReportDetail;

use App\Livewire\Pegawai\Dashboard as PegawaiDashboard;

use App\Livewire\Reports\CreateDailyReport;
use App\Livewire\Reports\MyDailyReports;
use App\Livewire\Reports\ShowDailyReport;
use App\Livewire\Reports\EditDailyReport;
use App\Livewire\Reports\ExportMonthlyReport;

use App\Livewire\Profile\ShowProfile;

use App\Http\Controllers\ReportPhotoController;
use App\Http\Controllers\DocumentationPenetapanController;
use App\Http\Controllers\DocumentationEvaluationController;
use App\Http\Controllers\DocumentationControlFollowUpController;
use App\Http\Controllers\DocumentationControlLetterController;
use App\Http\Controllers\DevelopmentPlanController;
use App\Http\Controllers\DevelopmentDocumentController;
use App\Http\Controllers\OperationalTicketController;
use App\Http\Controllers\PublicOperationalTicketController;
use App\Http\Controllers\AppNotificationController;
use App\Http\Controllers\OperationalRecordController;
use App\Http\Controllers\OperationalItemController;
use App\Http\Controllers\OperationalDocumentController;


use App\Services\ActivityLogger;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/tickets/create', [PublicOperationalTicketController::class, 'create'])
    ->name('public.tickets.create');

Route::post('/tickets', [PublicOperationalTicketController::class, 'store'])
    ->name('public.tickets.store');

Route::get('/tickets/success/{ticket:ticket_code}', [PublicOperationalTicketController::class, 'success'])
    ->name('public.tickets.success');

Route::get('/tickets/track', [PublicOperationalTicketController::class, 'trackForm'])
    ->name('public.tickets.track-form');

Route::post('/tickets/track', [PublicOperationalTicketController::class, 'track'])
    ->name('public.tickets.track');

Route::get('/tickets/kiosk', [PublicOperationalTicketController::class, 'kiosk'])
    ->name('public.tickets.kiosk');

Route::get('/tickets/track/{ticket:ticket_code}/{token}', [PublicOperationalTicketController::class, 'showTracking'])
    ->name('public.tickets.show-tracking');

Route::middleware(['auth', 'active.user', 'force.password.change'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role?->name;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kanit' => redirect()->route('kanit.dashboard'),
            'pegawai', 'gkm' => redirect()->route('pegawai.dashboard'),
            default => abort(403, 'Role user belum dikenali.'),
        };
    })->name('dashboard');

    Route::get('/profile', ShowProfile::class)
        ->name('profile.show');

    Route::get('/notifications', [AppNotificationController::class, 'index'])
    ->name('notifications.index');

    Route::patch('/notifications/read-all', [AppNotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    Route::patch('/notifications/{notification}/read', [AppNotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::get('/notifications/{notification}/open', [AppNotificationController::class, 'open'])
        ->name('notifications.open');

    Route::get('/documentation/penetapan', [DocumentationPenetapanController::class, 'index'])
        ->name('documentation.penetapan.index');

    Route::get('/documentation/penetapan/create', [DocumentationPenetapanController::class, 'create'])
        ->name('documentation.penetapan.create');

    Route::post('/documentation/penetapan', [DocumentationPenetapanController::class, 'store'])
        ->name('documentation.penetapan.store');

    Route::get('/documentation/penetapan/documents/{document}', [DocumentationPenetapanController::class, 'show'])
        ->name('documentation.penetapan.show');

    Route::get('/documentation/penetapan/documents/{document}/edit', [DocumentationPenetapanController::class, 'edit'])
        ->name('documentation.penetapan.edit');

    Route::put('/documentation/penetapan/documents/{document}', [DocumentationPenetapanController::class, 'update'])
        ->name('documentation.penetapan.update');

    Route::delete('/documentation/penetapan/documents/{document}', [DocumentationPenetapanController::class, 'destroy'])
        ->name('documentation.penetapan.destroy');

    Route::patch('/documentation/penetapan/documents/{document}/publish', [DocumentationPenetapanController::class, 'publish'])
        ->name('documentation.penetapan.publish');

    Route::patch('/documentation/penetapan/documents/{document}/archive', [DocumentationPenetapanController::class, 'archive'])
        ->name('documentation.penetapan.archive');

    Route::get('/documentation/penetapan/documents/{document}/download', [DocumentationPenetapanController::class, 'download'])
        ->name('documentation.penetapan.download');

    Route::get('/documentation/evaluasi/hasil-evaluasi', [DocumentationEvaluationController::class, 'index'])
        ->name('documentation.evaluasi.index');

    Route::get('/documentation/evaluasi/hasil-evaluasi/create', [DocumentationEvaluationController::class, 'create'])
        ->name('documentation.evaluasi.create');

    Route::post('/documentation/evaluasi/hasil-evaluasi', [DocumentationEvaluationController::class, 'store'])
        ->name('documentation.evaluasi.store');

    Route::get('/documentation/evaluasi/hasil-evaluasi/{record}', [DocumentationEvaluationController::class, 'show'])
        ->name('documentation.evaluasi.show');
        
    Route::get('/documentation/evaluasi/hasil-evaluasi/{record}/edit', [DocumentationEvaluationController::class, 'edit'])
        ->name('documentation.evaluasi.edit');

    Route::put('/documentation/evaluasi/hasil-evaluasi/{record}', [DocumentationEvaluationController::class, 'update'])
        ->name('documentation.evaluasi.update');    

    Route::patch('/documentation/evaluasi/hasil-evaluasi/{record}/publish', [DocumentationEvaluationController::class, 'publish'])
        ->name('documentation.evaluasi.publish');

    Route::patch('/documentation/evaluasi/hasil-evaluasi/{record}/archive', [DocumentationEvaluationController::class, 'archive'])
        ->name('documentation.evaluasi.archive');

    Route::delete('/documentation/evaluasi/hasil-evaluasi/{record}', [DocumentationEvaluationController::class, 'destroy'])
        ->name('documentation.evaluasi.destroy');

    Route::post('/documentation/evaluasi/hasil-evaluasi/{record}/documents', [DocumentationEvaluationController::class, 'storeDocument'])
        ->name('documentation.evaluasi.documents.store');

    Route::get('/documentation/evaluasi/documents/{document}/download', [DocumentationEvaluationController::class, 'downloadDocument'])
        ->name('documentation.evaluasi.documents.download');

    Route::delete('/documentation/evaluasi/documents/{document}', [DocumentationEvaluationController::class, 'destroyDocument'])
        ->name('documentation.evaluasi.documents.destroy');

    Route::prefix('/documentation/control')
        ->name('documentation.control.')
        ->group(function () {
            Route::get('/follow-ups', [DocumentationControlFollowUpController::class, 'index'])
                ->name('follow-ups.index');

            Route::get('/follow-ups/create', [DocumentationControlFollowUpController::class, 'create'])
                ->name('follow-ups.create');

            Route::post('/follow-ups', [DocumentationControlFollowUpController::class, 'store'])
                ->name('follow-ups.store');

            Route::get('/follow-ups/{followUp}/edit', [DocumentationControlFollowUpController::class, 'edit'])
                ->name('follow-ups.edit');

            Route::put('/follow-ups/{followUp}', [DocumentationControlFollowUpController::class, 'update'])
                ->name('follow-ups.update');

            Route::patch('/follow-ups/{followUp}/status', [DocumentationControlFollowUpController::class, 'updateStatus'])
                ->name('follow-ups.update-status');

            Route::patch('/follow-ups/{followUp}/progress', [DocumentationControlFollowUpController::class, 'updateProgress'])
                ->name('follow-ups.update-progress');

            Route::get('/follow-ups/{followUp}', [DocumentationControlFollowUpController::class, 'show'])
                ->name('follow-ups.show');

            Route::delete('/follow-ups/{followUp}', [DocumentationControlFollowUpController::class, 'destroy'])
                ->name('follow-ups.destroy');

            Route::post('/follow-ups/{followUp}/letters', [DocumentationControlFollowUpController::class, 'storeLetter'])
                ->name('follow-ups.letters.store');

            Route::get('/letters', [DocumentationControlLetterController::class, 'index'])
                ->name('letters.index');

            Route::get('/letters/create', [DocumentationControlLetterController::class, 'create'])
                ->name('letters.create');

            Route::post('/letters', [DocumentationControlLetterController::class, 'store'])
                ->name('letters.store');

            Route::get('/letters/{letter}/edit', [DocumentationControlLetterController::class, 'edit'])
                ->name('letters.edit');

            Route::put('/letters/{letter}', [DocumentationControlLetterController::class, 'update'])
                ->name('letters.update');

            Route::delete('/letters/{letter}', [DocumentationControlLetterController::class, 'destroy'])
                ->name('letters.destroy');

            Route::get('/letters/{letter}', [DocumentationControlLetterController::class, 'show'])
                ->name('letters.show');

            Route::get('/letters/{letter}/download', [DocumentationControlLetterController::class, 'download'])
                ->name('letters.download');
        });

    Route::prefix('operations')
        ->name('operations.')
        ->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Tiket Operasional
            |--------------------------------------------------------------------------
            |
            | Route baca dapat diakses role sesuai authorization controller.
            | Route pengelolaan status, PIC, dan delete diberi middleware role
            | sebagai lapisan proteksi tambahan.
            |
            */

            Route::get('/tickets', [OperationalTicketController::class, 'index'])
                ->name('tickets.index');

            Route::get('/tickets/create', [OperationalTicketController::class, 'create'])
                ->name('tickets.create');

            Route::post('/tickets', [OperationalTicketController::class, 'store'])
                ->name('tickets.store');

            Route::get('/tickets/{ticket}', [OperationalTicketController::class, 'show'])
                ->name('tickets.show');

            /*
            * Pegawai PIC dapat membuat laporan lanjutan.
            * Authorization detail tetap diperiksa di controller.
            */
            Route::post(
                '/tickets/{ticket}/continuation-report',
                [OperationalTicketController::class, 'createContinuationReport']
            )->name('tickets.continuation-report');

            /*
            * Admin, Kanit, GKM, atau PIC dapat menambahkan catatan sesuai
            * authorization yang diperiksa di controller.
            */
            Route::post(
                '/tickets/{ticket}/notes',
                [OperationalTicketController::class, 'storeNote']
            )->name('tickets.notes.store');

            /*
            * Aksi pengelolaan tiket hanya untuk Admin, Kanit, dan GKM.
            *
            * Scope unit untuk Kanit dan GKM tetap diperiksa oleh
            * authorizeTicketManageAccess() di controller.
            */
            Route::middleware('role:admin,kanit,gkm')
                ->group(function () {
                    Route::patch(
                        '/tickets/{ticket}/status',
                        [OperationalTicketController::class, 'updateStatus']
                    )->name('tickets.update-status');

                    Route::patch(
                        '/tickets/{ticket}/assignment',
                        [OperationalTicketController::class, 'updateAssignment']
                    )->name('tickets.update-assignment');

                    Route::delete(
                        '/tickets/{ticket}',
                        [OperationalTicketController::class, 'destroy']
                    )->name('tickets.destroy');
                });

            Route::get('/forms', [OperationalRecordController::class, 'index'])
                ->name('forms.index');

            Route::get('/items', [OperationalItemController::class, 'index'])
                ->name('items.index');

            Route::get('/items/import', [OperationalItemController::class, 'importForm'])
                ->name('items.import-form');

            Route::post('/items/import', [OperationalItemController::class, 'import'])
                ->name('items.import');

            Route::get('/items/create', [OperationalItemController::class, 'create'])
                ->name('items.create');

            Route::post('/items', [OperationalItemController::class, 'store'])
                ->name('items.store');

            Route::get('/items/{item}/edit', [OperationalItemController::class, 'edit'])
                ->name('items.edit');

            Route::patch('/items/{item}', [OperationalItemController::class, 'update'])
                ->name('items.update');

            Route::patch('/items/{item}/toggle-active', [OperationalItemController::class, 'toggleActive'])
                ->name('items.toggle-active');

            Route::get('/forms/create', [OperationalRecordController::class, 'create'])
                ->name('forms.create');

            Route::post('/forms', [OperationalRecordController::class, 'store'])
                ->name('forms.store');

            Route::get('/forms/{record}', [OperationalRecordController::class, 'show'])
                ->name('forms.show');

            Route::get('/forms/{record}/export/excel', [OperationalRecordController::class, 'exportExcel'])
                ->name('forms.export.excel');

            Route::patch('/forms/{record}/items/{item}', [OperationalRecordController::class, 'updateItem'])
                ->name('forms.items.update');

            Route::post('/forms/{record}/items', [OperationalRecordController::class, 'storeItem'])
                ->name('forms.items.store');

            Route::patch('/forms/{record}/submit', [OperationalRecordController::class, 'submit'])
                ->name('forms.submit');

            Route::patch('/forms/{record}/verify', [OperationalRecordController::class, 'verify'])
                ->name('forms.verify');

            Route::patch('/forms/{record}/cancel', [OperationalRecordController::class, 'cancel'])
                ->name('forms.cancel');

            Route::delete('/forms/{record}', [OperationalRecordController::class, 'destroy'])
                ->name('forms.destroy');

            Route::get('/documents', [OperationalDocumentController::class, 'index'])
                ->name('documents.index');

            Route::get('/documents/create', [OperationalDocumentController::class, 'create'])
                ->name('documents.create');

            Route::post('/documents', [OperationalDocumentController::class, 'store'])
                ->name('documents.store');

            Route::get('/documents/{document}/edit', [OperationalDocumentController::class, 'edit'])
                ->name('documents.edit');

            Route::put('/documents/{document}', [OperationalDocumentController::class, 'update'])
                ->name('documents.update');

            Route::get('/documents/{document}', [OperationalDocumentController::class, 'show'])
                ->name('documents.show');

            Route::get('/documents/{document}/download', [OperationalDocumentController::class, 'download'])
                ->name('documents.download');

            Route::patch('/documents/{document}/publish', [OperationalDocumentController::class, 'publish'])
                ->name('documents.publish');

            Route::patch('/documents/{document}/archive', [OperationalDocumentController::class, 'archive'])
                ->name('documents.archive');

            Route::delete('/documents/{document}', [OperationalDocumentController::class, 'destroy'])
                ->name('documents.destroy');

        });

    Route::get('/reports/photos/{photo}', [ReportPhotoController::class, 'show'])
    ->name('reports.photos.show');

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

        Route::get('/master-data/pegawai/{employee}/duties', ManageDuties::class)
            ->name('master-data.pegawai.duties');

        Route::get('/duty-delegations', DutyDelegationIndex::class)
            ->name('duty-delegations.index');

        Route::get('/master-data/server', ServerIndex::class)
            ->name('master-data.server.index');

        Route::get('/master-data/aplikasi', AplikasiIndex::class)
            ->name('master-data.aplikasi.index');

        Route::get('/master-data/template-laporan', ReportTemplateIndex::class)
            ->name('master-data.report-template.index'); 
            
        Route::get('/positions', PositionIndex::class)
            ->name('positions.index');
        
        Route::get('/user-management/missing-accounts', MissingAccounts::class)
            ->name('user-management.missing-accounts');

        Route::get('/user-management/users', UserManagementIndex::class)
            ->name('user-management.users.index');

        Route::get('/activity-logs', ActivityLogIndex::class)
            ->name('activity-logs');

        Route::get('/master-data/duty-classifications', DutyClassificationIndex::class)
            ->name('master-data.duty-classifications.index');

        Route::get('/unit-targets', UnitTargetIndex::class)
            ->name('unit-targets.index');

        Route::get('/target-reports', TargetReportIndex::class)
            ->name('target-reports.index');
    }); 

/*
|--------------------------------------------------------------------------
| Area khusus Kanit
|--------------------------------------------------------------------------
*/

Route::middleware(['kanit'])
    ->prefix('kanit')
    ->name('kanit.')
    ->group(function () {
        Route::get('/dashboard', KanitDashboard::class)
            ->name('dashboard');

        Route::get('/duty-delegations', DutyDelegationIndex::class)
            ->name('duty-delegations.index');
    });

/*
|--------------------------------------------------------------------------
| Area monitoring Kanit dan GKM
|--------------------------------------------------------------------------
*/

Route::middleware(['role:kanit,gkm'])
    ->prefix('kanit')
    ->name('kanit.')
    ->group(function () {
        Route::get('/monitoring-laporan', ReportMonitoring::class)
            ->name('reports.monitoring');

        Route::get('/monitoring-laporan/{report}', ReportDetail::class)
            ->name('reports.detail');

        Route::get('/unit-targets', UnitTargetIndex::class)
            ->name('unit-targets.index');

        Route::get('/target-reports', TargetReportIndex::class)
            ->name('target-reports.index');
    });

    Route::get('/reports/export/monthly', ExportMonthlyReport::class)
        ->middleware('role:admin,kanit,gkm')
        ->name('reports.export.monthly');

Route::middleware(['role:pegawai,gkm'])
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

Route::prefix('developments')
    ->name('developments.')
    ->group(function () {
        Route::get('/plans', [DevelopmentPlanController::class, 'index'])
            ->name('plans.index');

        Route::get('/plans/create', [DevelopmentPlanController::class, 'create'])
            ->name('plans.create');

        Route::post('/plans', [DevelopmentPlanController::class, 'store'])
            ->name('plans.store');

        Route::get('/plans/{developmentPlan}', [DevelopmentPlanController::class, 'show'])
            ->name('plans.show');

        Route::get('/plans/{developmentPlan}/edit', [DevelopmentPlanController::class, 'edit'])
            ->name('plans.edit');

        Route::put('/plans/{developmentPlan}', [DevelopmentPlanController::class, 'update'])
            ->name('plans.update');

        Route::delete('/plans/{developmentPlan}', [DevelopmentPlanController::class, 'destroy'])
            ->name('plans.destroy');

        Route::patch('/plans/{developmentPlan}/status', [DevelopmentPlanController::class, 'updateStatus'])
            ->name('plans.update-status');

        Route::patch('/plans/{developmentPlan}/progress', [DevelopmentPlanController::class, 'updateProgress'])
            ->name('plans.update-progress');

        Route::get('/documents', [DevelopmentDocumentController::class, 'index'])
            ->name('documents.index');

        Route::get('/documents/create', [DevelopmentDocumentController::class, 'create'])
            ->name('documents.create');

        Route::post('/documents', [DevelopmentDocumentController::class, 'store'])
            ->name('documents.store');

        Route::get('/documents/{developmentDocument}/edit', [DevelopmentDocumentController::class, 'edit'])
            ->name('documents.edit');

        Route::put('/documents/{developmentDocument}', [DevelopmentDocumentController::class, 'update'])
            ->name('documents.update');

        Route::delete('/documents/{developmentDocument}', [DevelopmentDocumentController::class, 'destroy'])
            ->name('documents.destroy');

        Route::get('/documents/{developmentDocument}/download', [DevelopmentDocumentController::class, 'download'])
            ->name('documents.download');
    });

});

require __DIR__.'/auth.php';