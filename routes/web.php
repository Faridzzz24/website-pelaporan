<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Publik — Tanpa Login ────────────────────────────────
Route::get('/', [ReportController::class, 'create'])->name('report.create');
Route::post('/report', [ReportController::class, 'store'])->name('report.store');
Route::get('/report/confirmation/{trackingCode}', [ReportController::class, 'confirmation'])->name('report.confirmation');
Route::get('/track', [ReportController::class, 'track'])->name('report.track');
Route::post('/track', [ReportController::class, 'trackResult'])->name('report.track.result');

// ── Auth ────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Dashboard — Wajib Login + Role Check ────────────────
Route::middleware(['auth', 'role:admin,hse_officer,supervisor'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports/{id}', [DashboardController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{id}/status', [DashboardController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::patch('/reports/{id}/assign', [DashboardController::class, 'assign'])->name('reports.assign');
    Route::delete('/reports/{id}', [DashboardController::class, 'destroy'])->name('reports.destroy');
    Route::get('/export', [DashboardController::class, 'export'])->name('reports.export');

    // Manajemen User (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
    });
});
