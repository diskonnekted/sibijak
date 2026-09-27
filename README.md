# SIBIJAK — Sistem Informasi Pembina Jasa Konstruksi & Fisik

Aplikasi web & mobile **Dinas Pekerjaan Umum dan Penataan Ruang (PUPR) Kabupaten
Banjarnegara** untuk memantau proyek fisik, mengevaluasi kinerja kontraktor,
melakukan pengawasan lapangan, serta pelaporan progres berbasis spasial.

---

## 🧱 Tech Stack

| Komponen | Teknologi |
| :--- | :--- |
| Framework | Laravel 12 |
| Bahasa | PHP ^8.2 |
| Database | SQLite (`database/database.sqlite`) |
| Peta | Leaflet |
| Frontend | Blade + Tailwind (responsif: desktop & mobile) |
| Upload | Storage lokal (`storage/app/public`, symlink `public/storage`) |

---

## 👤 Pengguna & Credentials Uji Coba

Semua akun seeder memakai password: `password`

| Peran (Role) | Alamat Email | Hak Akses |
| :--- | :--- | :--- |
| **Admin PUPR** (`admin_pupr`) | `admin@pupr.banjarnegara.go.id` | Kontrol penuh: CRUD kontraktor/proyek/pengawas, CMS, analisa, verifikasi akhir |
| **Kontraktor / Pelaksana** (`kontraktor`) | `kontraktor@sikap.id` | Kelola data perusahaan + lapor progres (foto) di lokasi proyek |
| **Pengawas Lapangan** (`pemeriksa_lapangan`) | `pemeriksa@sikap.id` | Verifikasi laporan lapangan + dokumentasi cross-check + riwayat verifikasi |

Kontraktor seeder lainnya (password sama): `serayuagung@sikap.id`,
`elektrindo@sikap.id`, `gumiwang@sikap.id`.

---

## 🔄 Alur Kerja Utama

### Approval Berlapis (dua lapis verifikasi)

```
Kontraktor            Pengawas Lapangan            Admin PUPR
lapor progres  ──▶  verifikasi lapangan (1)  ──▶  persetujuan akhir (2)
 (foto bukti)       approve / reject              final_approve / final_reject
```

1. **Kontraktor** mengajukan progres fisik baru + foto dokumentasi.
2. **Pengawas lapangan** memeriksa di lapangan, mengunggah **foto dokumentasi
   cross-check**, lalu menyetujui/menolak (lapisan 1).
3. **Admin PUPR** melakukan persetujuan akhir (lapisan 2) sebelum data progres
   diresmikan ke data spasial utama.

Status verifikasi: `pending`, `rejected`, `verified`, `clean`.

### Hak Akses dan Rute Portal

| Role | Rute |
| :--- | :--- |
| Publik | `/`, `/pekerjaan/{id}`, `/daftar`, `/badanusaha`, `/pelatihan`, `/regulasi`, `/berita` |
| Admin | `/admin` (dashboard, map, analisa, log, CMS, kontraktor, proyek, pengawas) |
| Kontraktor | `/kontraktor` (dashboard + lapor progres) |
| Pengawas | `/pengawas` (verifikasi + peta + riwayat verifikasi) |
| Login | `/login`, `/admin/login`, `/kontraktor/login`, `/pengawas/login` |

> Middleware `role` memisahkan akses: `admin_pupr`, `kontraktor`, `pemeriksa_lapangan`.
> Route `/sim-login/{user}` hanya aktif di environment `local` (untuk berganti peran saat dev).

---

## 🌐 API Publik Data Pekerjaan

Untuk konsumen eksternal (inspektorat, OPD, aplikasi mitra). Base URL: `/api`.

| Method | Endpoint | Keterangan |
| :--- | :--- | :--- |
| GET | `/api/proyek` | Daftar pekerjaan. Filter: `?status=`, `?verification_status=`, `?tahun=`, `?search=`, `?per_page=` |
| GET | `/api/proyek/{id}` | Detail pekerjaan + riwayat verifikasi + foto dokumentasi |
| GET | `/api/statistik` | Rekapitulasi total, nilai kontrak, rata-rata progress, per status |

**Autentikasi** — kirim header `X-API-KEY: <kunci>` (nilai diatur lewat `API_KEY`
di `.env`). Jika `API_KEY` kosong, endpoint terbuka (mode dev lokal).

Contoh request:

```bash
curl -H "X-API-KEY: inspektorat-2026-sibijak" http://127.0.0.1:8086/api/proyek
curl -H "X-API-KEY: inspektorat-2026-sibijak" "http://127.0.0.1:8086/api/proyek?status=Pelaksanaan&tahun=2025"
```

Payload pekerjaan memuat: `nama_pekerjaan`, `pelaksana`, `nilai_kontrak`,
`tahun_anggaran`, `status`, `progress`, `status_verifikasi`, `pengawas`, `lokasi`
(lat/long), `jadwal`, dan info verifikasi berlapis.

---

## 📦 Instalasi Lokal

Prasyarat: PHP 8.2+, Composer, ekstensi `sqlite3`.

### Opsi A — Migrasi + Seeder (bersih)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Akses: `http://127.0.0.1:8086` (atau port dari `php artisan serve`).

Seeder membuat 23 proyek, 229 foto, kontraktor, dan pengawas demo lengkap.

### Opsi B — Restore Dump SQL (data dummy lengkap)

Dump penuh tersedia di `database/sibijak-dump.sql`:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
sqlite3 database/database.sqlite < database/sibijak-dump.sql
php artisan storage:link
php artisan serve
```

---

## 🗂️ Struktur Penting

```
app/Http/Controllers/DashboardController.php   # controller utama (hampir semua fitur)
app/Http/Controllers/Api/ProyekApiController.php # API publik data pekerjaan
app/Http/Middleware/RoleMiddleware.php         # gate role
app/Http/Middleware/ApiKeyMiddleware.php       # gate API key
routes/web.php                                 # rute web (portal, admin, kontraktor, pengawas)
routes/api.php                                 # rute API publik
database/seeders/DatabaseSeeder.php            # data dummy + foto proyek
database/sibijak-dump.sql                      # dump SQL lengkap (opsi restore cepat)
public/storage -> storage/app/public           # upload foto & dokumen
public/pekerjaan-*-dummy/                      # sumber foto dummy (jalan, gedung, jembatan, air)
```

---

## 📝 Fitur

- **Proyek fisik** — CRUD, lokasi spasial (lat/long), ruas jalan (geojson), jadwal, nilai kontrak, progres.
- **Monitoring progres** — lapor progres kontraktor + foto per tahap, timeline & galeri foto.
- **Approval berlapis** — verifikasi pengawas → persetujuan akhir admin.
- **Dokumentasi & cross-check** — unggah foto multi (maks. 8) oleh pengawas.
- **Penugasan pengawas** — `pengawas_id` per proyek + riwayat verifikasi di dasbor pengawas.
- **Badan usaha / kontraktor** — registrasi, verifikasi admin, profil & rating.
- **CMS** — pelatihan, regulasi (upload PDF + kategori + unduh), berita (upload foto cover + kategori).
- **Analisa & rekomendasi** — tab analisa admin + log aktivitas (audit).
- **API publik** — data pekerjaan untuk integrasi lintas sistem.

---

## ⚙️ Catatan

- `APP_URL` di `.env.example` memakai `http://127.0.0.1:8086`; sesuaikan dengan port server Anda.
- Upload foto disimpan di `storage/app/public`, jangan lupa `php artisan storage:link` setelah install.
- Database live (`database/database.sqlite`) **tidak di-commit** (terdaftar di `.gitignore`).
  Gunakan `database/sibijak-dump.sql` untuk berbagi data dummy antar lingkungan.