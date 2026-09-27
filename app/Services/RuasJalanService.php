<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

/**
 * Layanan pembaca data geospasial "ruas jalan".
 *
 * Sumber utamanya adalah public/ruas_jalan.geojson (hasil pembersihan yang
 * garis-garisnya sudah mengikuti jaringan jalan sebenarnya). Setiap fitur
 * memiliki properties: id, nomor_ruas, nama_ruas, titik_awal, titik_akhir,
 * panjang_km, lebar_m — dan geometry LineString.
 */
class RuasJalanService
{
    /** @var array|null Cache hasil decode koleksi GeoJSON. */
    protected static ?array $collection = null;

    /**
     * Muat (dan cache) keseluruhan FeatureCollection.
     */
    public static function load(): array
    {
        if (static::$collection !== null) {
            return static::$collection;
        }

        $path = public_path('ruas_jalan.geojson');
        if (!File::exists($path)) {
            static::$collection = ['type' => 'FeatureCollection', 'features' => []];

            return static::$collection;
        }

        $decoded = json_decode(File::get($path), true);
        static::$collection = is_array($decoded)
            ? $decoded
            : ['type' => 'FeatureCollection', 'features' => []];

        return static::$collection;
    }

    /**
     * Daftar ringkasan seluruh ruas (untuk dropdown / API).
     * Kembalian tiap item berupa array ber-key id, nomor_ruas, nama_ruas,
     * panjang_km, lebar_m, titik_awal, titik_akhir, lat, lng.
     */
    public static function all(): array
    {
        $out = [];
        foreach (static::load()['features'] ?? [] as $feature) {
            $props = $feature['properties'] ?? [];
            $id = $props['id'] ?? null;
            if ($id === null) {
                continue;
            }

            $coords = $feature['geometry']['coordinates'] ?? [];
            $first = $coords[0] ?? null;

            $out[] = [
                'id'          => (int) $id,
                'nomor_ruas'  => $props['nomor_ruas'] ?? null,
                'nama_ruas'   => $props['nama_ruas'] ?? null,
                'titik_awal'  => $props['titik_awal'] ?? null,
                'titik_akhir' => $props['titik_akhir'] ?? null,
                'panjang_km'  => isset($props['panjang_km']) ? (float) $props['panjang_km'] : 0,
                'lebar_m'     => isset($props['lebar_m']) ? (float) $props['lebar_m'] : 0,
                // Titik representatif (koordinat pertama) untuk pemetaan.
                'lng'         => $first ? (float) $first[0] : null,
                'lat'         => $first ? (float) $first[1] : null,
            ];
        }

        return $out;
    }

    /**
     * Ambil satu ruas berdasarkan id (properties.id pada GeoJSON).
     */
    public static function find(int $id): ?array
    {
        foreach (static::all() as $ruas) {
            if ($ruas['id'] === $id) {
                return $ruas;
            }
        }

        return null;
    }

    /**
     * Cari ruas jalan terdekat dari sebuah koordinat (lat, lng).
     * Mengembalikan ruas + jarak (meter) terdekat, atau null bila kosong.
     */
    public static function findNearest(float $lat, float $lng): ?array
    {
        $best = null;
        $bestDistance = PHP_FLOAT_MAX;

        foreach (static::load()['features'] ?? [] as $feature) {
            $props = $feature['properties'] ?? [];
            $id = $props['id'] ?? null;
            if ($id === null) {
                continue;
            }

            $coords = $feature['geometry']['coordinates'] ?? [];
            if (count($coords) < 2) {
                continue;
            }

            // Jarak minimum titik -> tiap segmen garis pada ruas ini.
            $min = PHP_FLOAT_MAX;
            for ($i = 1; $i < count($coords); $i++) {
                $d = static::distanceToSegment(
                    $lat,
                    $lng,
                    (float) $coords[$i - 1][1],
                    (float) $coords[$i - 1][0],
                    (float) $coords[$i][1],
                    (float) $coords[$i][0]
                );
                if ($d < $min) {
                    $min = $d;
                }
            }

            if ($min < $bestDistance) {
                $bestDistance = $min;
                $best = [
                    'id'           => (int) $id,
                    'nomor_ruas'   => $props['nomor_ruas'] ?? null,
                    'nama_ruas'    => $props['nama_ruas'] ?? null,
                    'titik_awal'   => $props['titik_awal'] ?? null,
                    'titik_akhir'  => $props['titik_akhir'] ?? null,
                    'panjang_km'   => isset($props['panjang_km']) ? (float) $props['panjang_km'] : 0,
                    'lebar_m'      => isset($props['lebar_m']) ? (float) $props['lebar_m'] : 0,
                    'distance_m'   => round($min, 1),
                ];
            }
        }

        return $best;
    }

    /**
     * Jarak (meter) titik ke segmen garis a-b menggunakan haversine
     * proyeksi sederhana (akurat untuk skala kabupaten).
     */
    protected static function distanceToSegment(
        float $lat,
        float $lng,
        float $a_lat,
        float $a_lng,
        float $b_lat,
        float $b_lng
    ): float {
        // Proyeksi equirectangular lokal (meter) untuk menghitung titik proyeksi.
        $tx = $lng * 111320 * cos(deg2rad($lat));
        $ty = $lat * 110540;

        $ax = $a_lng * 111320 * cos(deg2rad($a_lat));
        $ay = $a_lat * 110540;
        $bx = $b_lng * 111320 * cos(deg2rad($b_lat));
        $by = $b_lat * 110540;

        $dx = $bx - $ax;
        $dy = $by - $ay;

        $lenSq = $dx * $dx + $dy * $dy;
        if ($lenSq == 0.0) {
            // Segmen merosot jadi titik.
            return static::haversine($lat, $lng, $a_lat, $a_lng);
        }

        // Parameter proyeksi (0..1) titik terdekat pada segmen.
        $t = (($tx - $ax) * $dx + ($ty - $ay) * $dy) / $lenSq;
        $t = max(0.0, min(1.0, $t));

        $px = $ax + $t * $dx;
        $py = $ay + $t * $dy;

        $pLng = $px / (111320 * cos(deg2rad($lat)));
        $pLat = $py / 110540;

        return static::haversine($lat, $lng, $pLat, $pLng);
    }

    /**
     * Haversine distance (meter).
     */
    public static function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $r * asin(sqrt($a));
    }

    /**
     * Summary ringkas (jumlah ruas & panjang) untuk panel admin.
     */
    public static function stats(): array
    {
        $all = static::all();
        $totalLength = array_sum(array_column($all, 'panjang_km'));

        return [
            'total_ruas'      => count($all),
            'total_panjang'   => round($totalLength, 2),
        ];
    }
}