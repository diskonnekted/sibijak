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
        \App\Models\Project::create([
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
            'tanggal_deadline' => '2026-10-01'
        ]);

        \App\Models\Project::create([
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
            'tanggal_deadline' => '2026-09-15'
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

        \App\Models\Project::create([
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
            'tanggal_deadline' => '2026-12-15'
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
            'name' => 'Pemeriksa Lapangan PUPR',
            'email' => 'pemeriksa@sikap.id',
            'password' => bcrypt('password'),
            'role' => 'pemeriksa_lapangan',
        ]);
    }
}
