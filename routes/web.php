<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public Portal
Route::get('/', [DashboardController::class, 'index'])->name('portal');

// Sibijak Content Menus
Route::get('/badanusaha', [DashboardController::class, 'badanUsaha'])->name('badanusaha');
Route::get('/badanusaha/{contractor}', [DashboardController::class, 'publicShowContractor'])->name('badanusaha.show');
Route::get('/pekerjaan/{project}', [DashboardController::class, 'publicShowProject'])->name('pekerjaan.show');
Route::get('/pelatihan', [DashboardController::class, 'pelatihan'])->name('pelatihan');
Route::get('/regulasi', [DashboardController::class, 'regulasi'])->name('regulasi');
Route::get('/berita', [DashboardController::class, 'berita'])->name('berita');
Route::get('/daftar', [DashboardController::class, 'daftar'])->name('daftar');

// Registration submission
Route::post('/daftar/badanusaha', [DashboardController::class, 'submitDaftarBadanUsaha'])->name('daftar.badanusaha');
Route::post('/daftar/pelatihan', [DashboardController::class, 'submitDaftarPelatihan'])->name('daftar.pelatihan');

// Authentication routes (Hub & Dedicated)
Route::get('/login', [DashboardController::class, 'showLogin'])->name('login');
Route::post('/login', [DashboardController::class, 'login']);

Route::get('/admin/login', [DashboardController::class, 'showAdminLogin'])->name('login.admin');
Route::post('/admin/login', [DashboardController::class, 'adminLogin']);

Route::get('/kontraktor/login', [DashboardController::class, 'showKontraktorLogin'])->name('login.kontraktor');
Route::post('/kontraktor/login', [DashboardController::class, 'kontraktorLogin']);

Route::get('/pengawas/login', [DashboardController::class, 'showPengawasLogin'])->name('login.pengawas');
Route::post('/pengawas/login', [DashboardController::class, 'pengawasLogin']);

Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

// Simulated Login (For switcher inside admin panel)
Route::get('/sim-login/{user}', function (\App\Models\User $user) {
    Auth::login($user);
    if ($user->role === 'kontraktor') return redirect()->route('kontraktor.dashboard');
    if ($user->role === 'pemeriksa_lapangan') return redirect()->route('pengawas.dashboard');
    return redirect()->route('admin.dashboard');
})->name('sim-login')->middleware('auth');

// Admin Dashboard protected by auth & role:admin_pupr
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin_pupr'])->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/map', [DashboardController::class, 'adminMap'])->name('map');
    Route::get('/analisa', [DashboardController::class, 'adminAnalysis'])->name('analysis');
    Route::get('/logs', [DashboardController::class, 'activityLogs'])->name('logs');
    Route::get('/contractors/{contractor}', [DashboardController::class, 'showContractor'])->name('contractors.show');
    
    // Contractors CRUD
    Route::post('/contractors', [DashboardController::class, 'storeContractor'])->name('contractors.store');
    Route::put('/contractors/{contractor}', [DashboardController::class, 'updateContractor'])->name('contractors.update');
    Route::delete('/contractors/{contractor}', [DashboardController::class, 'deleteContractor'])->name('contractors.delete');
    
    // Projects CRUD
    Route::post('/projects', [DashboardController::class, 'storeProject'])->name('projects.store');
    Route::put('/projects/{project}', [DashboardController::class, 'updateProject'])->name('projects.update');
    Route::delete('/projects/{project}', [DashboardController::class, 'deleteProject'])->name('projects.delete');
});

// Portal Kontraktor (Responsive Desktop & Mobile)
Route::prefix('kontraktor')->name('kontraktor.')->middleware(['auth', 'role:kontraktor'])->group(function () {
    Route::get('/', [DashboardController::class, 'mobileContractor'])->name('dashboard');
    Route::post('/report/{project}', [DashboardController::class, 'mobileContractorSubmitReport'])->name('report');
});

// Portal Pengawas (Responsive Desktop & Mobile)
Route::prefix('pengawas')->name('pengawas.')->middleware(['auth', 'role:pemeriksa_lapangan'])->group(function () {
    Route::get('/', [DashboardController::class, 'mobileSupervisor'])->name('dashboard');
    Route::post('/verify/{project}', [DashboardController::class, 'mobileSupervisorVerifyReport'])->name('verify');
});

// Backward Compatibility Aliases (/mobile/* -> clean routes)
Route::get('/mobile/kontraktor', function () { return redirect()->route('kontraktor.dashboard'); })->name('mobile.kontraktor');
Route::post('/mobile/kontraktor/report/{project}', [DashboardController::class, 'mobileContractorSubmitReport'])->name('mobile.kontraktor.report');
Route::get('/mobile/pengawas', function () { return redirect()->route('pengawas.dashboard'); })->name('mobile.pengawas');
Route::post('/mobile/pengawas/verify/{project}', [DashboardController::class, 'mobileSupervisorVerifyReport'])->name('mobile.pengawas.verify');
