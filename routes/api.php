<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProyekApiController;

/*
|--------------------------------------------------------------------------
| API Routes (Publik untuk integrasi lintas sistem)
|--------------------------------------------------------------------------
|
| Endpoint data pekerjaan SIBIJAK untuk dikonsumsi aplikasi eksternal
| seperti pengawasan inspektorat, OPD, dan pihak terkait lainnya.
|
| Autentikasi: kirim header `X-API-KEY: <kunci>` (lihat API_KEY di .env).
| Jika API_KEY kosong di .env, endpoint terbuka (mode pengembangan lokal).
|
*/

Route::middleware('api.key')->group(function () {
    Route::get('/proyek', [ProyekApiController::class, 'index']);
    Route::get('/proyek/{project}', [ProyekApiController::class, 'show']);
    Route::get('/statistik', [ProyekApiController::class, 'statistik']);
});