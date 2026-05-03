<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Kanit\Dashboard as KanitDashboard;
use App\Livewire\Pegawai\Dashboard as PegawaiDashboard;

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

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:kanit'])->prefix('kanit')->name('kanit.')->group(function () {
        Route::get('/dashboard', KanitDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
        Route::get('/dashboard', PegawaiDashboard::class)->name('dashboard');
    });

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

require __DIR__.'/auth.php';