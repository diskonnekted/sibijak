<?php

namespace App\Http\Controllers;

use App\Services\RuasJalanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RuasJalanController extends Controller
{
    /**
     * Daftar seluruh ruas jalan (untuk dropdown & referensi client).
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'ruas'  => RuasJalanService::all(),
            'stats' => RuasJalanService::stats(),
        ]);
    }

    /**
     * Cari ruas jalan terdekat dari koordinat tertentu.
     * GET /admin/ruas-jalan/nearest?lat=..&lng=..
     */
    public function nearest(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return response()->json(['error' => 'Parameter lat/lng wajib berupa angka.'], 422);
        }

        $ruas = RuasJalanService::findNearest((float) $lat, (float) $lng);

        if ($ruas === null) {
            return response()->json(['found' => false]);
        }

        return response()->json(['found' => true, 'ruas' => $ruas]);
    }
}