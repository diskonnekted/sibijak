<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RuasJalanController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

// Public Portal
Route::get('/', [DashboardController::class, 'index'])->name('portal');

// Sibijak Content Menus
Route::get('/badanusaha', [DashboardController::class, 'badanUsaha'])->name('badanusaha');
Route::get('/badanusaha/{contractor}', [DashboardController::class, 'publicShowContractor'])->name('badanusaha.show');
Route::get('/pekerjaan/{project}', [DashboardController::class, 'publicShowProject'])->name('pekerjaan.show');
Route::get('/pelatihan', [DashboardController::class, 'pelatihan'])->name('pelatihan');
Route::get('/regulasi', [DashboardController::class, 'regulasi'])->name('regulasi');
Route::get('/regulasi/{regulation}/download', [DashboardController::class, 'downloadRegulation'])->name('regulasi.download');
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
// SECURITY: hanya aktif di environment lokal. Di produksi route ini TIDAK terdaftar
// sehingga tidak bisa dipakai untuk privilege escalation antar role.
if (app()->environment('local')) {
    Route::get('/sim-login/{user}', function (\App\Models\User $user) {
        Auth::login($user);
        if ($user->role === 'kontraktor') return redirect()->route('kontraktor.dashboard');
        if ($user->role === 'pemeriksa_lapangan') return redirect()->route('pengawas.dashboard');
        return redirect()->route('admin.dashboard');
    })->name('sim-login')->middleware('auth');
}

// Lupa & reset kata sandi (password reset flow)
Route::get('/password/forgot', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.update');

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

    // Verifikasi pendaftaran badan usaha (approve/reject + auto-create akun)
    Route::post('/contractors/{contractor}/approve', [DashboardController::class, 'approveContractor'])->name('contractors.approve');
    Route::post('/contractors/{contractor}/reject', [DashboardController::class, 'rejectContractor'])->name('contractors.reject');
    
    // Projects CRUD
    Route::post('/projects', [DashboardController::class, 'storeProject'])->name('projects.store');
    Route::put('/projects/{project}', [DashboardController::class, 'updateProject'])->name('projects.update');
    Route::delete('/projects/{project}', [DashboardController::class, 'deleteProject'])->name('projects.delete');

    // Approval berlapis: persetujuan akhir admin (lapisan final)
    Route::post('/projects/{project}/final-verify', [DashboardController::class, 'adminFinalVerifyProject'])->name('projects.final-verify');

    // Referensi Ruas Jalan (geojson) untuk proyek berbasis ruas jalan
    Route::get('/ruas-jalan', [RuasJalanController::class, 'index'])->name('ruas-jalan.index');
    Route::get('/ruas-jalan/nearest', [RuasJalanController::class, 'nearest'])->name('ruas-jalan.nearest');

    // CMS Konten & Publikasi (Pelatihan, Regulasi, Berita)
    Route::get('/cms', [DashboardController::class, 'cms'])->name('cms');

    Route::post('/cms/pelatihan', [DashboardController::class, 'storeTraining'])->name('cms.pelatihan.store');
    Route::put('/cms/pelatihan/{training}', [DashboardController::class, 'updateTraining'])->name('cms.pelatihan.update');
    Route::delete('/cms/pelatihan/{training}', [DashboardController::class, 'deleteTraining'])->name('cms.pelatihan.delete');

    Route::post('/cms/regulasi', [DashboardController::class, 'storeRegulation'])->name('cms.regulasi.store');
    Route::put('/cms/regulasi/{regulation}', [DashboardController::class, 'updateRegulation'])->name('cms.regulasi.update');
    Route::delete('/cms/regulasi/{regulation}', [DashboardController::class, 'deleteRegulation'])->name('cms.regulasi.delete');

    Route::post('/cms/berita', [DashboardController::class, 'storeNews'])->name('cms.berita.store');
    Route::put('/cms/berita/{news}', [DashboardController::class, 'updateNews'])->name('cms.berita.update');
    Route::delete('/cms/berita/{news}', [DashboardController::class, 'deleteNews'])->name('cms.berita.delete');

    // Manajemen Pengawas Lapangan
    Route::post('/pengawas', [DashboardController::class, 'storePengawas'])->name('pengawas.store');
    Route::put('/pengawas/{pengawas}', [DashboardController::class, 'updatePengawas'])->name('pengawas.update');
    Route::delete('/pengawas/{pengawas}', [DashboardController::class, 'deletePengawas'])->name('pengawas.delete');
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
