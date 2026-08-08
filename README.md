# SIBIJAK Banjarnegara - Sistem Informasi Pembina Jasa Konstruksi & Fisik

Aplikasi Web & Mobile Dinas Pekerjaan Umum dan Penataan Ruang (PUPR) Kabupaten Banjarnegara untuk melakukan monitoring proyek fisik, evaluasi kinerja kontraktor, pengawasan berkala, serta pelaporan progres berbasis spasial.

---

## 👥 Pengguna Uji Coba & Hak Akses (Credentials)

Sistem menggunakan enkripsi bawaan dan dapat diuji menggunakan data seeder berikut (Password untuk semua akun: `password`):

| Peran (Role) | Alamat Email | Target Utama & Hak Akses |
| :--- | :--- | :--- |
| **Admin PUPR** | `admin@pupr.banjarnegara.go.id` | Akses Kontrol Penuh (CRUD Kontraktor/Proyek, Analisa & Rekomendasi, Verifikasi Pengajuan) |
| **Kontraktor (Pelaksana)** | `kontraktor@sikap.id` | Mengelola data perusahaan sendiri & melakukan pelaporan foto progres di lokasi proyek |
| **Pengawas Lapangan** | `pemeriksa@sikap.id` | Memantau seluruh proyek & memverifikasi progress fisik yang diajukan oleh kontraktor |

---

## 📱 Panduan Penggunaan Halaman Mobile

Aplikasi seluler didesain responsif dengan navigasi menu bawah (*Bottom Navigation Bar*) untuk menunjang aktivitas lapangan.

### 👷 1. Alur Kerja Peran Kontraktor (Pelaksana)
Rute Akses: `http://127.0.0.1:8000/mobile/kontraktor`

1. **Masuk Log (Log In)**: Akses rute mobile menggunakan email `kontraktor@sikap.id`. Sistem akan mengarahkan secara otomatis ke dashboard mobile kontraktor.
2. **Tab Proyek (Bottom Nav - Proyek)**: Halaman utama menampilkan daftar paket pekerjaan fisik yang sedang dikerjakan oleh perusahaan pelaksana terkait lengkap dengan status target dan progress bar.
3. **Mengajukan Progres Baru (Lapor)**:
   * Klik tombol **"Laporkan Progres Baru"** pada kartu pekerjaan.
   * Masukkan persentase capaian kemajuan fisik baru (%) pada kolom isian.
   * Pilih berkas foto dokumentasi visual proyek terbaru menggunakan tombol unggah foto.
   * Klik **"Kirim Laporan"**. Pengajuan akan masuk ke dalam antrean pemeriksaan dengan status *Menunggu Review*.

---

### 🔍 2. Alur Kerja Peran Pengawas Lapangan (Pemeriksa)
Rute Akses: `http://127.0.0.1:8000/mobile/pengawas`

1. **Masuk Log (Log In)**: Akses rute mobile menggunakan email `pemeriksa@sikap.id`.
2. **Tab Verifikasi (Bottom Nav - Verifikasi)**:
   * Menampilkan daftar antrean laporan progress fisik yang diajukan oleh para kontraktor pelaksana.
   * Menampilkan rincian nama pekerjaan, nama kontraktor, persentase kenaikan progres, serta preview gambar/foto bukti fisik proyek di lapangan.
   * Pengawas dapat mengeklik **Setujui** untuk memvalidasi progress ke dalam data spasial utama, atau mengeklik **Tolak** untuk membatalkan pengajuan.
3. **Tab Peta (Bottom Nav - Peta Spasial)**:
   * Menampilkan peta Leaflet interaktif sebaran lokasi pekerjaan di Kabupaten Banjarnegara.
   * Dilengkapi dengan penanda lingkaran dinamis berwarna merah (kritis), kuning (sedang), atau hijau (aman) sesuai dengan status progress fisik.
   * Klik penanda (*marker*) untuk memunculkan ringkasan detail pekerjaan.

---

## 🖥️ Integrasi Dasbor Utama Desktop (Admin & Pengawas)
Rute Akses: `http://127.0.0.1:8000/admin`

* **Notifikasi Pengajuan Progres**: Jika login sebagai Admin PUPR atau Pengawas Lapangan melalui browser desktop, panel khusus **"Verifikasi Laporan Progres Baru"** akan otomatis muncul di bagian teratas dasbor apabila terdapat antrean verifikasi aktif yang belum diproses oleh pengawas lapangan.
* **Aksi Cepat**: Admin dapat menolak atau menyetujui pengajuan progres lengkap dengan foto bukti fisik proyek secara langsung dari dasbor desktop.

---

## 🛠️ Langkah Instalasi Lokal

Lakukan langkah-langkah berikut di terminal untuk menjalankan proyek ini secara lokal:

1. **Unduh Dependensi Composer**:
   ```bash
   composer install
   ```

2. **Salin Environment file & Set Key**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Inisialisasi Database, Migrasi, & Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser Anda melalui alamat: `http://127.0.0.1:8000`.
