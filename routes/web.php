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

// Authenticaton routes
Route::get('/login', [DashboardController::class, 'showLogin'])->name('login');
Route::post('/login', [DashboardController::class, 'login']);
Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

// Simulated Login (For switcher inside admin panel)
Route::get('/sim-login/{user}', function (\App\Models\User $user) {
    Auth::login($user);
    return redirect()->route('admin.dashboard');
})->name('sim-login')->middleware('auth');

// Admin Dashboard protected by auth
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/map', [DashboardController::class, 'adminMap'])->name('map');
    Route::get('/analisa', [DashboardController::class, 'adminAnalysis'])->name('analysis');
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
