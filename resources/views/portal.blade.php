@extends('layouts.sibijak')

@section('title', 'SIBIJAK Banjarnegara - Beranda')

@section('content')
  <!-- SWIPER BANNER HERO -->
  <section class="relative bg-slate-900 text-white py-28 overflow-hidden border-b border-slate-800">
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-25" style="background-image: url('assets/contractor_hero.jpg');"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-gov-950/90 to-transparent"></div>
    
    <div class="max-w-7xl mx-auto px-6 relative z-10 space-y-6">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-gov-100/10 border border-white/10 text-slate-300 text-[11px] font-bold tracking-wider uppercase">
        Pemerintah Kabupaten Banjarnegara
      </div>
      <h1 class="text-4xl md:text-5xl lg:text-[54px] font-extrabold tracking-tighter leading-[1.05] max-w-[20ch]">
        Sistem Informasi Pembinaan Jasa Konstruksi
      </h1>
      <p class="text-base text-slate-300 max-w-[55ch] leading-relaxed">
        Portal pengawasan tertib usaha, tertib penyelenggaraan, dan peningkatan kompetensi tenaga kerja konstruksi Dinas PUPR Kabupaten Banjarnegara.
      </p>
      <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
        <a href="{{ route('badanusaha') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-xs font-bold tracking-wider uppercase text-gov-950 bg-white hover:bg-slate-100 rounded active:scale-[0.98] transition-all">
          Direktori Kontraktor
        </a>
        <a href="{{ route('daftar') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-xs font-bold tracking-wider uppercase text-white hover:text-slate-200 border border-white/20 bg-white/5 backdrop-blur-sm rounded active:scale-[0.98] transition-all">
          Pendaftaran Online
        </a>
      </div>
    </div>
  </section>

  <!-- TENTANG SIBIJAK -->
  <section class="py-24 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      <div class="lg:col-span-7 space-y-6">
        <span class="text-[11px] font-mono font-bold tracking-[0.2em] text-gov-600 uppercase block">TENTANG PORTAL</span>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-none">Apa itu SIBIJAK?</h2>
        <p class="text-sm text-slate-600 leading-relaxed">
          Sistem Informasi Pembinaan Jasa Konstruksi (SIBIJAK) Kabupaten Banjarnegara merupakan sistem informasi dalam bentuk website yang dikelola oleh Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Banjarnegara sebagai pembina jasa konstruksi.
        </p>
        <p class="text-sm text-slate-600 leading-relaxed">
          Sistem ini dibangun dalam rangka meningkatkan kemudahan akses informasi usaha jasa konstruksi bagi masyarakat dalam memberikan dan menyebarkan informasi yang transparan sehingga membantu perkuatan jaringan bisnis pelaku usaha jasa konstruksi. SIBIJAK bertujuan sebagai alat pengawasan tertib usaha, tertib penyelenggaraan, dan tertib pemanfaatan jasa konstruksi.
        </p>
      </div>
      <div class="lg:col-span-5 bg-slate-50 p-8 rounded border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tugas & Fungsi Bidang Jasa Konstruksi</h3>
        <div class="space-y-3">
          <div class="flex gap-3 text-xs leading-relaxed text-slate-600">
            <i class="ph-bold ph-check text-gov-600 text-sm mt-0.5"></i>
            <span>Menyusun rencana program kerja lingkup Bina Konstruksi.</span>
          </div>
          <div class="flex gap-3 text-xs leading-relaxed text-slate-600">
            <i class="ph-bold ph-check text-gov-600 text-sm mt-0.5"></i>
            <span>Melaksanakan sertifikasi dan pelatihan tenaga terampil konstruksi.</span>
          </div>
          <div class="flex gap-3 text-xs leading-relaxed text-slate-600">
            <i class="ph-bold ph-check text-gov-600 text-sm mt-0.5"></i>
            <span>Melaksanakan pengelolaan sistem informasi jasa konstruksi terpadu.</span>
          </div>
          <div class="flex gap-3 text-xs leading-relaxed text-slate-600">
            <i class="ph-bold ph-check text-gov-600 text-sm mt-0.5"></i>
            <span>Melaksanakan pengawasan tertib usaha konstruksi daerah.</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- QUICK STATS (REPLACED MAP WITH STATIC SUMMARY) -->
  <section class="py-20 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded border border-slate-200 shadow-sm space-y-3">
          <div class="w-12 h-12 rounded bg-gov-50 text-gov-600 flex items-center justify-center">
            <i class="ph-bold ph-buildings text-2xl"></i>
          </div>
          <h3 class="text-base font-bold text-slate-900">Total Kemitraan</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Terdiri dari {{ $stats['total_contractors'] }} kontraktor lokal dan nasional terverifikasi administrasi.
          </p>
        </div>

        <div class="bg-white p-8 rounded border border-slate-200 shadow-sm space-y-3">
          <div class="w-12 h-12 rounded bg-gov-50 text-gov-600 flex items-center justify-center">
            <i class="ph-bold ph-hard-hat text-2xl"></i>
          </div>
          <h3 class="text-base font-bold text-slate-900">Pekerjaan Aktif</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Memantau {{ $stats['total_projects'] }} paket infrastruktur daerah secara real-time oleh pengawas dinas.
          </p>
        </div>

        <div class="bg-white p-8 rounded border border-slate-200 shadow-sm space-y-3">
          <div class="w-12 h-12 rounded bg-gov-50 text-gov-600 flex items-center justify-center">
            <i class="ph-bold ph-trend-up text-2xl"></i>
          </div>
          <h3 class="text-base font-bold text-slate-900">Efisiensi Lapangan</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Rata-rata progres capaian pekerjaan fisik berada di angka {{ number_format($stats['average_progress'], 1) }}%.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- BERITA TERBARU -->
  <section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
      <div class="flex justify-between items-end mb-12">
        <div>
          <span class="text-[11px] font-mono font-bold tracking-[0.2em] text-gov-600 uppercase block mb-2">UPDATE INFORMASI</span>
          <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-none">Kabar Konstruksi Banjarnegara</h2>
        </div>
        <a href="{{ route('berita') }}" class="text-xs font-bold uppercase tracking-wider text-gov-900 hover:text-gov-950 flex items-center gap-1.5 transition-all">
          Lihat Semua Berita <i class="ph ph-arrow-right"></i>
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($recentNews as $n)
          <div class="bg-slate-50 p-6 rounded border border-slate-200 hover:shadow-sm transition-shadow flex flex-col justify-between min-h-[220px]">
            <div>
              <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase mb-3">
                <span>{{ $n->kategori }}</span>
                <span>{{ date('d M Y', strtotime($n->tanggal)) }}</span>
              </div>
              <h3 class="text-base font-extrabold text-slate-900 mb-2 line-clamp-2 hover:text-gov-900">
                <a href="{{ route('berita') }}">{{ $n->judul }}</a>
              </h3>
              <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">
                {{ $n->konten }}
              </p>
            </div>
            <a href="{{ route('berita') }}" class="text-[11px] font-bold tracking-wider text-gov-900 hover:text-gov-950 uppercase inline-flex items-center gap-1 mt-6">
              Baca Lengkap <i class="ph ph-caret-right"></i>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </section>
@endsection
