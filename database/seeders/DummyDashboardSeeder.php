<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\Project;
use App\Services\RuasJalanService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeder dummy komprehensif untuk pengujian dashboard.
 *
 * Membuat:
 *  - 6 kontraktor (status approved) lintas bidang usaha (Sipil, Arsitektur,
 *    Mekanikal) dengan kualifikasi beragam.
 *  - Proyek PERBAIKAN RUAS JALAN yang ditautkan ke ruas jalan nyata dari
 *    public/ruas_jalan.geojson (kolom ruas_jalan_id/nomor/nama) dengan status
 *    bervariasi: Persiapan, Pelaksanaan, Selesai.
 *  - Proyek NON-JALAN (gedung, irigasi, air bersih, dsb.) tanpa tautan ruas,
 *    tersebar di berbagai titik Kabupaten Banjarnegara.
 *
 * Idempotent: kontraktor di-firstOrCreate berdasarkan NIB, proyek di-
 * updateOrCreate berdasarkan nama_pekerjaan (sehingga status/progres selalu
 * konsisten dengan target bila seeder dijalankan ulang).
 */
class DummyDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Membuat dummy data dashboard (kontraktor + proyek jalan & non-jalan)...');

        $this->cleanupPreviousDummy();
        $this->seedContractors();
        $this->seedRoadProjects();
        $this->seedNonRoadProjects();

        $this->command?->info('Selesai. Jalankan ulang kapan saja — aman (idempotent).');
    }

    /**
     * Hapus dummy dari versi seeder sebelumnya (berdasarkan NIB sintetis)
     * agar hasil selalu bersih & konsisten saat dijalankan ulang.
     */
    protected function cleanupPreviousDummy(): void
    {
        $nibs = [
            '9120100012341', '9120200056782', '9120300098763',
            '9120400043214', '9120500067890', '9120600013579',
        ];

        $ids = Contractor::whereIn('nib', $nibs)->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }

        Project::whereIn('contractor_id', $ids)->delete();
        Contractor::whereIn('nib', $nibs)->delete();

        $this->command?->line('  - Dummy lama dibersihkan ('.$ids->count().' kontraktor + proyeknya).');
    }

    // ------------------------------------------------------------------
    // 1. KONTRAKTOR
    // ------------------------------------------------------------------
    protected function seedContractors(): void
    {
        $defs = [
            // Bidang Sipil — fokus jalan & infrastruktur
            ['name' => 'PT. Karya Banjarnegara Abadi',   'bidang' => 'Sipil',      'kualifikasi' => 'Besar',    'nib' => '9120100012341', 'pj' => 'H. Bambang Sutrisno, ST',        'alamat' => 'Jl. Mayjen Bambang Sugeng No. 45, Banjarnegara',        'email' => 'info@karyabanjarnegara.co.id', 'telepon' => '0286-591234', 'rating' => 4.80],
            ['name' => 'CV. Mandiraja Perkasa',           'bidang' => 'Sipil',      'kualifikasi' => 'Menengah', 'nib' => '9120200056782', 'pj' => 'Dewi Anggraini, S.T., M.T.',      'alamat' => 'Jl. Raya Banjarnegara - Mandiraja KM 7, Purwareja Klampok', 'email' => 'admin@mandirajaperkasa.id', 'telepon' => '0286-597845', 'rating' => 4.50],
            ['name' => 'PT. Serayu Infra Nusantara',      'bidang' => 'Sipil',      'kualifikasi' => 'Menengah', 'nib' => '9120300098763', 'pj' => 'Ir. Agus Salim Wijaya',           'alamat' => 'Jl. DI Panjaitan No. 12, Bawang, Banjarnegara',          'email' => 'kontak@serayuinfra.com',      'telepon' => '0286-596032', 'rating' => 4.30],
            // Non-jalan: gedung & bangunan
            ['name' => 'CV. Griya Banjarnegara Konstruksi','bidang' => 'Arsitektur','kualifikasi' => 'Menengah', 'nib' => '9120400043214', 'pj' => 'Rina Kusumawardani, S.Ars',         'alamat' => 'Jl. S. Parman No. 8, Banjarnegara',                    'email' => 'griya.bna@gmail.com',        'telepon' => '0286-594120', 'rating' => 4.60],
            // Non-jalan: mekanikal & perpipaan
            ['name' => 'PT. Tirta Serayu Engineering',    'bidang' => 'Mekanikal',  'kualifikasi' => 'Menengah', 'nib' => '9120500067890', 'pj' => 'Fajar Nugroho, S.T.',             'alamat' => 'Jl. Raya Pucang KM 3, Bawang, Banjarnegara',           'email' => 'office@tirtaserayu.co.id',   'telepon' => '0286-598877', 'rating' => 4.40],
            // Non-jalan: sipil skala kecil (irigasi desa, talud, embung)
            ['name' => 'CV. Sumber Rejeki Konstruksi',    'bidang' => 'Sipil',      'kualifikasi' => 'Kecil',    'nib' => '9120600013579', 'pj' => 'Muh. Ardiansyah',                 'alamat' => 'Jl. Kalibening - Banjarnegara KM 2, Kalibening',        'email' => 'sumberrejeki@gmail.com',     'telepon' => '0286-595390', 'rating' => 4.10],
        ];

        foreach ($defs as $def) {
            Contractor::firstOrCreate(
                ['nib' => $def['nib']],
                array_merge($def, ['status' => 'approved'])
            );
        }

        $this->command?->line('  ✓ 6 kontraktor (approved) disiapkan.');
    }

    // ------------------------------------------------------------------
    // 2. PROYEK PERBAIKAN RUAS JALAN (taut ruas + status bervariasi)
    // ------------------------------------------------------------------
    protected function seedRoadProjects(): void
    {
        $ruasList = collect(RuasJalanService::all())
            ->filter(fn ($r) => $r['nama_ruas'] !== null && $r['nomor_ruas'] !== null)
            ->values();

        if ($ruasList->isEmpty()) {
            $this->command?->warn('  ! ruas_jalan.geojson kosong/tidak ada — proyek jalan dilewati.');

            return;
        }

        // Kontraktor sipil dummy yang menangani perbaikan jalan (dirotasi).
        $sipilContractors = Contractor::whereIn('nib', [
            '9120100012341', // PT. Karya Banjarnegara Abadi
            '9120200056782', // CV. Mandiraja Perkasa
            '9120300098763', // PT. Serayu Infra Nusantara
            '9120600013579', // CV. Sumber Rejeki Konstruksi
        ])->orderBy('id')->get();
        if ($sipilContractors->isEmpty()) {
            $sipilContractors = collect([Contractor::first()]);
        }

        // [template_nama, nilai_kontrak, tahun_anggaran, status, progress]
        $tpl = [
            ['Perbaikan Ruas Jalan :nama',            680000000, 2026, 'Pelaksanaan', 42],
            ['Peningkatan Jalan :nama',               1450000000,2025, 'Pelaksanaan', 66],
            ['Pemeliharaan Berkala Jalan :nama',      385000000, 2024, 'Selesai',    100],
            ['Rehabilitasi Jalan :nama',              1180000000,2026, 'Persiapan',    4],
            ['Perbaikan Drainase & Bahu Jalan :nama', 495000000, 2023, 'Selesai',    100],
            ['Pelebaran Jalan :nama',                 2350000000,2026, 'Persiapan',    2],
        ];

        $ruasTerpilih = $this->pickRuas($ruasList, count($tpl));

        $created = 0;
        $updated = 0;

        foreach ($ruasTerpilih as $i => $ruas) {
            $t = $tpl[$i % count($tpl)];
            $nama = str_replace(':nama', $ruas['nama_ruas'], $t[0]);
            $sn = $sipilContractors[$i % $sipilContractors->count()];

            $project = Project::updateOrCreate(
                ['nama_pekerjaan' => $nama],
                [
                    'contractor_id'       => $sn->id,
                    'nilai_kontrak'       => $t[1],
                    'tahun_anggaran'      => $t[2],
                    'status'              => $t[3],
                    'progress'            => $t[4],
                    'latitude'            => (float) $ruas['lat'],
                    'longitude'           => (float) $ruas['lng'],
                    'detail_lokasi'       => 'Ruas '.trim(($ruas['titik_awal'] ?? '').' — '.($ruas['titik_akhir'] ?? ''), ' —'),
                    'tanggal_kontrak'     => $this->kontrak($t[3]),
                    'tanggal_pelaksanaan' => $this->pelaksanaan($t[3]),
                    'tanggal_pemeriksaan' => $this->pemeriksaan($t[3]),
                    'tanggal_deadline'    => $this->deadline($t[3]),
                    'ruas_jalan_id'       => $ruas['id'],
                    'ruas_jalan_nomor'    => $ruas['nomor_ruas'],
                    'ruas_jalan_nama'     => $ruas['nama_ruas'],
                ]
            );

            $project->wasRecentlyCreated ? $created++ : $updated++;
            $this->command?->line("  • {$nama} [TA {$t[2]} · {$t[3]} {$t[4]}%] → {$sn->name} (ruas {$ruas['nomor_ruas']})");
        }

        $this->command?->line("  ✓ Proyek jalan: {$created} baru, {$updated} diperbarui.");
    }

    // ------------------------------------------------------------------
    // 3. PROYEK NON-JALAN (tanpa ruas, tersebar, status bervariasi)
    // ------------------------------------------------------------------
    protected function seedNonRoadProjects(): void
    {
        $c = fn (string $nib) => Contractor::where('nib', $nib)->first();

        $griya   = $c('9120400043214');
        $tirta   = $c('9120500067890');
        $sumber  = $c('9120600013579');
        $serayu  = $c('9120300098763');

        // [nama, kontraktor, nilai_kontrak, tahun, lat, lng, status, progress, detail_lokasi]
        $items = [
            ['Pembangunan Gedung Kantor Kecamatan Sigaluh',  $griya,  950000000,  2025, -7.4320, 109.6690, 'Pelaksanaan', 55, 'Jl. Raya Banjarnegara - Banyumas, Sigaluh'],
            ['Rehabilitasi Gedung SD Negeri 2 Kecitran',     $griya,  620000000,  2024, -7.4580, 109.4310, 'Selesai',    100, 'Desa Kecitran, Kec. Purwareja Klampok'],
            ['Pembangunan Pasar Rakyat Mandiraja',           $griya,  2750000000, 2026, -7.4720, 109.5160, 'Persiapan',    3, 'Desa Mandiraja Kulon, Kec. Mandiraja'],
            ['Pembangunan Jaringan Irigasi Tersier D.I. Merawu', $sumber, 520000000, 2025, -7.3960, 109.6800, 'Pelaksanaan', 38, 'Kec. Banjarnegara'],
            ['Normalisasi Sungai Serayu (Segment Kalibening)', $serayu, 3200000000, 2026, -7.3100, 109.7300, 'Pelaksanaan', 21, 'Kec. Kalibening'],
            ['Rehabilitasi Talud & Embung Desa Penanggungan', $sumber, 360000000, 2023, -7.3500, 109.7100, 'Selesai',   100, 'Desa Penanggungan, Kec. Wanayasa'],
            ['Pembangunan Sarana Air Bersih (Hidran Umum)',  $tirta,  480000000,  2025, -7.4000, 109.6250, 'Pelaksanaan', 60, 'Kec. Bawang'],
            ['Pengadaan & Pemasangan Pompa Irigasi Portable', $tirta, 185000000,  2024, -7.3300, 109.5850, 'Selesai',    100, 'Kec. Punggelan'],
            ['Pembangunan TPS 3R Desa Gumiwang',             $sumber, 290000000,  2026, -7.3800, 109.7000, 'Persiapan',    5, 'Desa Gumiwang, Kec. Purwanegara'],
            ['Rehabilitasi Jembatan Gantung Desa Slatri',    $serayu,  1290000000, 2024, -7.4300, 109.5000, 'Pelaksanaan', 47, 'Desa Slatri, Kec. Karangkobar'],
        ];

        $created = 0;
        $updated = 0;
        foreach ($items as $it) {
            [$nama, $kontraktor, $nilai, $tahun, $lat, $lng, $status, $progress, $detail] = $it;

            if (! $kontraktor) {
                $kontraktor = Contractor::where('status', 'approved')->first();
            }

            $project = Project::updateOrCreate(
                ['nama_pekerjaan' => $nama],
                [
                    'contractor_id'       => $kontraktor->id,
                    'nilai_kontrak'       => $nilai,
                    'tahun_anggaran'      => $tahun,
                    'status'              => $status,
                    'progress'            => $progress,
                    'latitude'            => $lat,
                    'longitude'           => $lng,
                    'detail_lokasi'       => $detail.', Kab. Banjarnegara',
                    'tanggal_kontrak'     => $this->kontrak($status),
                    'tanggal_pelaksanaan' => $this->pelaksanaan($status),
                    'tanggal_pemeriksaan' => $this->pemeriksaan($status),
                    'tanggal_deadline'    => $this->deadline($status),
                    'ruas_jalan_id'       => null,
                    'ruas_jalan_nomor'    => null,
                    'ruas_jalan_nama'     => null,
                ]
            );

            $project->wasRecentlyCreated ? $created++ : $updated++;
            $this->command?->line("  • {$nama} [TA {$tahun} · {$status} {$progress}%] → {$kontraktor->name}");
        }

        $this->command?->line("  ✓ Proyek non-jalan: {$created} baru, {$updated} diperbarui.");
    }

    // ------------------------------------------------------------------
    // Helper tanggal berdasar status
    // ------------------------------------------------------------------
    protected function kontrak(string $status): string
    {
        return match ($status) {
            'Selesai'     => Carbon::now()->subMonths(9)->format('Y-m-d'),
            'Pelaksanaan' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            default       => Carbon::now()->subMonth()->format('Y-m-d'),      // Persiapan
        };
    }

    protected function pelaksanaan(string $status): string
    {
        return match ($status) {
            'Selesai'     => Carbon::now()->subMonths(8)->format('Y-m-d'),
            'Pelaksanaan' => Carbon::now()->subMonths(2)->format('Y-m-d'),
            default       => Carbon::now()->addMonth()->format('Y-m-d'),
        };
    }

    protected function pemeriksaan(string $status): string
    {
        return match ($status) {
            'Selesai'     => Carbon::now()->subWeek()->format('Y-m-d'),
            'Pelaksanaan' => Carbon::now()->addMonths(1)->format('Y-m-d'),
            default       => Carbon::now()->addMonths(3)->format('Y-m-d'),
        };
    }

    protected function deadline(string $status): string
    {
        return match ($status) {
            'Selesai'     => Carbon::now()->subMonth()->format('Y-m-d'),
            'Pelaksanaan' => Carbon::now()->addMonths(2)->format('Y-m-d'),
            default       => Carbon::now()->addMonths(4)->format('Y-m-d'),
        };
    }

    /**
     * Pilih N ruas secara deterministik (tersebar merata) agar hasil stabil.
     */
    protected function pickRuas($ruasList, int $count): array
    {
        $total = $ruasList->count();
        if ($total === 0) {
            return [];
        }

        $indices = [];
        for ($i = 0; $i < $count; $i++) {
            $indices[] = (int) round($i * ($total - 1) / max(1, $count - 1));
        }

        return collect($indices)
            ->unique()
            ->map(fn ($idx) => $ruasList[$idx])
            ->values()
            ->all();
    }
}