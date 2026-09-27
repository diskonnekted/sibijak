<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin PUPR Banjarnegara',
            'email' => 'admin@pupr.banjarnegara.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin_pupr',
        ]);

        // Seed Contractors
        $c1 = \App\Models\Contractor::create([
            'name' => 'PT Banjarnegara Prima Karya',
            'bidang' => 'Sipil',
            'kualifikasi' => 'Besar',
            'nib' => '9120108745672',
            'pj' => 'Ir. H. Budi Santoso, M.T.',
            'alamat' => 'Jl. Pemuda No. 12, Banjarnegara',
            'email' => 'contact@banjarnegaraprima.co.id',
            'telepon' => '0286-591234',
            'rating' => 4.8
        ]);

        $c2 = \App\Models\Contractor::create([
            'name' => 'PT Serayu Agung Konstruksi',
            'bidang' => 'Arsitektur',
            'kualifikasi' => 'Menengah',
            'nib' => '8120409854321',
            'pj' => 'Rian Hidayat, S.T.',
            'alamat' => 'Jl. Diponegoro No. 88, Madukara, Banjarnegara',
            'email' => 'info@serayuagung.com',
            'telepon' => '0286-592345',
            'rating' => 4.6
        ]);

        $c3 = \App\Models\Contractor::create([
            'name' => 'CV Elektrindo Banjarnegara',
            'bidang' => 'Mekanikal',
            'kualifikasi' => 'Kecil',
            'nib' => '1220908756312',
            'pj' => 'Suwito Wibowo',
            'alamat' => 'Jl. Gatot Subroto No. 4, Karangkobar, Banjarnegara',
            'email' => 'cv.elektrindobjr@gmail.com',
            'telepon' => '0812-3456-7890',
            'rating' => 4.5
        ]);

        $c4 = \App\Models\Contractor::create([
            'name' => 'PT Gumiwang Pembangunan',
            'bidang' => 'Sipil',
            'kualifikasi' => 'Besar',
            'nib' => '9120207865412',
            'pj' => 'Ir. Ahmad Fauzi',
            'alamat' => 'Jl. Raya Purwanegara Km 8, Banjarnegara',
            'email' => 'gumiwang.bangun@outlook.com',
            'telepon' => '0286-593456',
            'rating' => 4.9
        ]);

        // Seed Projects
        $jembatan = \App\Models\Project::create([
            'contractor_id' => $c1->id,
            'nama_pekerjaan' => 'Pembangunan Jembatan Sungai Serayu (Pacing)',
            'nilai_kontrak' => 4500000000,
            'tahun_anggaran' => 2026,
            'status' => 'Pelaksanaan',
            'progress' => 65.50,
            'latitude' => -7.38289,
            'longitude' => 109.68212,
            'detail_lokasi' => 'Kecamatan Madukara, perbatasan dengan Kecamatan Sigaluh.',
            'tanggal_kontrak' => '2026-03-01',
            'tanggal_pelaksanaan' => '2026-03-10',
            'tanggal_pemeriksaan' => '2026-08-01',
            'tanggal_deadline' => '2026-10-01',
            'reported_progress' => 75.00,
            'reported_photo' => '/storage/projects/jembatan-tahap4-02.jpeg',
            'reported_at' => now(),
            'verification_status' => 'pending'
        ]);

        $jalan = \App\Models\Project::create([
            'contractor_id' => $c1->id,
            'nama_pekerjaan' => 'Rehabilitasi Jalan Diponegoro (Kota Banjarnegara)',
            'nilai_kontrak' => 1800000000,
            'tahun_anggaran' => 2026,
            'status' => 'Pelaksanaan',
            'progress' => 85.00,
            'latitude' => -7.39891,
            'longitude' => 109.69932,
            'detail_lokasi' => 'Ruas jalan protokol kota Banjarnegara depan Kodim.',
            'tanggal_kontrak' => '2026-02-15',
            'tanggal_pelaksanaan' => '2026-03-01',
            'tanggal_pemeriksaan' => '2026-08-05',
            'tanggal_deadline' => '2026-09-15',
            'reported_progress' => 92.50,
            'reported_photo' => '/storage/projects/jalan-tahap4-05.jpeg',
            'reported_at' => now(),
            'verification_status' => 'pending'
        ]);

        \App\Models\Project::create([
            'contractor_id' => $c2->id,
            'nama_pekerjaan' => 'Pembangunan Puskesmas Banjarnegara II',
            'nilai_kontrak' => 2750000000,
            'tahun_anggaran' => 2026,
            'status' => 'Selesai',
            'progress' => 100.00,
            'latitude' => -7.40456,
            'longitude' => 109.68453,
            'detail_lokasi' => 'Kelurahan Semarang, Kecamatan Banjarnegara.',
            'tanggal_kontrak' => '2026-01-10',
            'tanggal_pelaksanaan' => '2026-01-20',
            'tanggal_pemeriksaan' => '2026-06-30',
            'tanggal_deadline' => '2026-07-01'
        ]);

        $gedung = \App\Models\Project::create([
            'contractor_id' => $c2->id,
            'nama_pekerjaan' => 'Pembangunan Gedung Perpustakaan Daerah Banjarnegara',
            'nilai_kontrak' => 3500000000,
            'tahun_anggaran' => 2026,
            'status' => 'Pelaksanaan',
            'progress' => 40.00,
            'latitude' => -7.39750,
            'longitude' => 109.69550,
            'detail_lokasi' => 'Kecamatan Banjarnegara, samping Kantor Dinas Pendidikan & Kebudayaan.',
            'tanggal_kontrak' => '2026-04-01',
            'tanggal_pelaksanaan' => '2026-04-15',
            'tanggal_pemeriksaan' => '2026-08-01',
            'tanggal_deadline' => '2026-11-30',
            'reported_photo' => '/storage/projects/gedung-tahap3-04.jpeg',
            'verification_status' => 'clean'
        ]);

        \App\Models\Project::create([
            'contractor_id' => $c2->id,
            'nama_pekerjaan' => 'Revitalisasi Alun-alun Kota Banjarnegara',
            'nilai_kontrak' => 1200000000,
            'tahun_anggaran' => 2025,
            'status' => 'Selesai',
            'progress' => 100.00,
            'latitude' => -7.39678,
            'longitude' => 109.69722,
            'detail_lokasi' => 'Pusat Kota Banjarnegara, penataan trotoar dan taman.',
            'tanggal_kontrak' => '2025-05-01',
            'tanggal_pelaksanaan' => '2025-05-15',
            'tanggal_pemeriksaan' => '2025-11-20',
            'tanggal_deadline' => '2025-11-25'
        ]);

        \App\Models\Project::create([
            'contractor_id' => $c3->id,
            'nama_pekerjaan' => 'Instalasi Penerangan Jalan Umum (PJU) Karangkobar',
            'nilai_kontrak' => 650000000,
            'tahun_anggaran' => 2026,
            'status' => 'Pelaksanaan',
            'progress' => 45.00,
            'latitude' => -7.28472,
            'longitude' => 109.71213,
            'detail_lokasi' => 'Ruas jalan Karangkobar menuju Pejawaran.',
            'tanggal_kontrak' => '2026-04-10',
            'tanggal_pelaksanaan' => '2026-04-20',
            'tanggal_pemeriksaan' => '2026-08-01',
            'tanggal_deadline' => '2026-11-15'
        ]);

        $irigasi = \App\Models\Project::create([
            'contractor_id' => $c4->id,
            'nama_pekerjaan' => 'Peningkatan Saluran Irigasi Wanadadi',
            'nilai_kontrak' => 3100000000,
            'tahun_anggaran' => 2026,
            'status' => 'Persiapan',
            'progress' => 10.00,
            'latitude' => -7.36873,
            'longitude' => 109.62312,
            'detail_lokasi' => 'Kecamatan Wanadadi, melayani area persawahan irigasi primer.',
            'tanggal_kontrak' => '2026-07-01',
            'tanggal_pelaksanaan' => '2026-07-15',
            'tanggal_pemeriksaan' => '2026-08-05',
            'tanggal_deadline' => '2026-12-15',
            'reported_photo' => '/storage/projects/air-tahap1-03.jpeg'
        ]);

        // Seed Trainings
        $t1 = \App\Models\Training::create([
            'nama_pelatihan' => 'Bimbingan Teknis Tenaga Terampil Konstruksi (Pelaksana Pekerjaan Jalan)',
            'tanggal' => '2026-09-15',
            'kuota' => 30,
            'pendaftar_count' => 12,
            'status' => 'Mendatang',
            'detail' => 'Pelatihan kompetensi sertifikasi pelaksana lapangan jalan tingkat kabupaten.'
        ]);

        $t2 = \App\Models\Training::create([
            'nama_pelatihan' => 'Pelatihan Sistem Manajemen Keselamatan & Kesehatan Kerja (SMK3) Konstruksi',
            'tanggal' => '2026-08-20',
            'kuota' => 25,
            'pendaftar_count' => 25,
            'status' => 'Berjalan',
            'detail' => 'Pelatihan wajib K3 konstruksi bagi mandor dan supervisor proyek.'
        ]);

        // Attach trainings to contractors
        $c1->trainings()->attach([$t1->id, $t2->id]);
        $c2->trainings()->attach([$t2->id]);
        $c3->trainings()->attach([$t1->id]);
        $c4->trainings()->attach([$t2->id]);

        // Seed Regulations
        \App\Models\Regulation::create([
            'judul' => 'Undang-Undang Republik Indonesia Nomor 2 Tahun 2017 tentang Jasa Konstruksi',
            'nomor' => 'UU No. 2 Tahun 2017',
            'tahun' => 2017,
            'kategori' => 'Undang-Undang',
            'deskripsi' => 'Regulasi utama dasar penyelenggaraan usaha dan rantai pasok jasa konstruksi di Indonesia.'
        ]);

        \App\Models\Regulation::create([
            'judul' => 'Peraturan Pemerintah Nomor 22 Tahun 2020 tentang Peraturan Pelaksanaan UU No. 2 Tahun 2017',
            'nomor' => 'PP No. 22 Tahun 2020',
            'tahun' => 2020,
            'kategori' => 'Peraturan Pemerintah',
            'deskripsi' => 'Rincian aturan pelaksanaan teknis jasa konstruksi dan pembinaan usaha.'
        ]);

        // Seed News
        \App\Models\NewsItem::create([
            'judul' => 'Dinas PUPR Banjarnegara Gelar Pelatihan Sertifikasi Tenaga Kerja Konstruksi',
            'konten' => 'Guna meningkatkan kompetensi SDM konstruksi di Kabupaten Banjarnegara, Dinas PUPR menggelar sertifikasi tenaga terampil dengan standar nasional.',
            'tanggal' => '2026-08-01',
            'kategori' => 'Pelatihan'
        ]);

        \App\Models\NewsItem::create([
            'judul' => 'Pemberitahuan Wajib Pemenuhan Sertifikat Standar Usaha bagi Kontraktor Lokal',
            'konten' => 'Diimbau kepada seluruh rekanan penyedia jasa untuk segera melakukan pembaruan SBU melalui portal SIKAP guna kelancaran administrasi tender proyek.',
            'tanggal' => '2026-08-05',
            'kategori' => 'Pengumuman'
        ]);

        User::factory()->create([
            'name' => 'PT Banjarnegara Prima Karya (Pelaksana)',
            'email' => 'kontraktor@sikap.id',
            'password' => bcrypt('password'),
            'role' => 'kontraktor',
            'contractor_id' => $c1->id,
        ]);

        User::factory()->create([
            'name' => 'PT Serayu Agung Konstruksi (Pelaksana)',
            'email' => 'serayuagung@sikap.id',
            'password' => bcrypt('password'),
            'role' => 'kontraktor',
            'contractor_id' => $c2->id,
        ]);

        User::factory()->create([
            'name' => 'CV Elektrindo Banjarnegara (Pelaksana)',
            'email' => 'elektrindo@sikap.id',
            'password' => bcrypt('password'),
            'role' => 'kontraktor',
            'contractor_id' => $c3->id,
        ]);

        User::factory()->create([
            'name' => 'PT Gumiwang Pembangunan (Pelaksana)',
            'email' => 'gumiwang@sikap.id',
            'password' => bcrypt('password'),
            'role' => 'kontraktor',
            'contractor_id' => $c4->id,
        ]);

        User::factory()->create([
            'name' => 'Pemeriksa Lapangan PUPR',
            'email' => 'pemeriksa@sikap.id',
            'password' => bcrypt('password'),
            'role' => 'pemeriksa_lapangan',
        ]);

        // Seed Project Logs & Galeri Foto (tahap yang sudah berjalan)
        $kontraktor1 = \App\Models\User::where('email', 'kontraktor@sikap.id')->first();
        $kontraktor2 = \App\Models\User::where('email', 'serayuagung@sikap.id')->first();
        $kontraktor4 = \App\Models\User::where('email', 'gumiwang@sikap.id')->first();
        $pemeriksa = \App\Models\User::where('email', 'pemeriksa@sikap.id')->first();
        $admin = \App\Models\User::where('email', 'admin@pupr.banjarnegara.go.id')->first();

        $attachPhotos = function ($project, array $tahaps, $progress, $user, $withHistory) use ($pemeriksa, $admin) {
            $log = \App\Models\ProjectLog::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'action' => 'submission',
                'progress' => $progress,
                'photo' => null,
                'note' => 'Pengajuan progres fisik '.number_format($progress, 0).'% diajukan oleh penyedia jasa.',
            ]);

            $first = true;
            foreach ($tahaps as $tahap => $count) {
                for ($n = 1; $n <= $count; $n++) {
                    $path = 'storage/projects/'.$tahap.'-'.str_pad($n, 2, '0', STR_PAD_LEFT).'.jpeg';
                    \App\Models\ProjectPhoto::create([
                        'project_id' => $project->id,
                        'project_log_id' => $log->id,
                        'path' => $path,
                        'caption' => 'Foto progres '.$tahap,
                    ]);
                    if ($first) {
                        $log->update(['photo' => $path]);
                        $first = false;
                    }
                }
            }

            if ($withHistory) {
                \App\Models\ProjectLog::create([
                    'project_id' => $project->id,
                    'user_id' => $pemeriksa->id,
                    'action' => 'approve',
                    'progress' => $progress,
                    'photo' => null,
                    'note' => 'Inspeksi pengawas lapangan disetujui. Dokumentasi merupakan data yang valid.',
                ]);
                \App\Models\ProjectLog::create([
                    'project_id' => $project->id,
                    'user_id' => $admin->id,
                    'action' => 'final_approve',
                    'progress' => $progress,
                    'photo' => null,
                    'note' => 'Persetujuan akhir diberikan; progres tercatat resmi.',
                ]);
            }
        };

        $attachPhotos($jembatan, ['jembatan-tahap1' => 2, 'jembatan-tahap2' => 2, 'jembatan-tahap3' => 3, 'jembatan-tahap4' => 2], 75.00, $kontraktor1, false);
        $attachPhotos($jalan, ['jalan-tahap1' => 3, 'jalan-tahap2' => 6, 'jalan-tahap3' => 8, 'jalan-tahap4' => 5], 92.50, $kontraktor1, false);
        $attachPhotos($gedung, ['gedung-tahap1' => 3, 'gedung-tahap2' => 4, 'gedung-tahap3' => 4], 40.00, $kontraktor2, true);
        $attachPhotos($irigasi, ['air-tahap1' => 3], 10.00, $kontraktor4, false);
    }
}
