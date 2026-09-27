<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Pengawas Lapangan - SIBIJAK Banjarnegara</title>
  
  <!-- Font and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Leaflet CSS & JS for Spatial Map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <!-- Tailwind CSS & VDNA Theme -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            heading: ['Outfit', 'sans-serif'],
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            gov: {
              50: '#f0fdf4',
              100: '#dcfce7',
              600: '#16a34a',
              750: '#15803d',
              800: '#15803d',
              850: '#115e59',
              900: '#0f3d24',
              950: '#041f10',
            }
          }
        }
      }
    }
  </script>
  <style>
    :root {
      --vibe-background: #020617;
      --vibe-surface: #0f172a;
      --vibe-text-main: #f8fafc;
      --vibe-text-sub: #94a3b8;
      --vibe-accent-1: #0f3d24;
      --vibe-accent-2: #16a34a;
      --vibe-font-head: 'Outfit', sans-serif;
      --vibe-font-main: 'Plus Jakarta Sans', sans-serif;
      --radius-md: 16px;
      --vibe-transition: all 0.2s ease-in-out;
    }
    h1, h2, h3, h4, .font-heading {
      font-family: var(--vibe-font-head);
    }
    .leaflet-container {
      background: #020617 !important;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
  </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen antialiased">

  <!-- ========================================================= -->
  <!-- 1. MODE DESKTOP / LAPTOP VIEW (Tampil pada Layar md:block >=768px) -->
  <!-- ========================================================= -->
  <div class="hidden md:flex flex-col min-h-screen">
    <!-- NAVBAR TOP DESKTOP -->
    <header class="bg-slate-900/95 border-b border-slate-800 sticky top-0 z-30 shadow-md backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded bg-gov-600 flex items-center justify-center text-white font-bold shadow-sm text-lg">
            <i class="ph-bold ph-shield-check"></i>
          </div>
          <div>
            <div class="inline-flex items-center gap-1.5 text-[9px] font-bold tracking-wider uppercase text-slate-300">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>PEMERINTAH KABUPATEN BANJARNEGARA</span>
            </div>
            <h1 class="text-sm font-extrabold text-white leading-none font-heading uppercase">SIBIJAK PENGAWAS LAPANGAN</h1>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-right hidden sm:block">
            <span class="block text-xs font-bold text-slate-200 leading-none font-heading">{{ $user->name }}</span>
            <span class="text-[10px] text-gov-400 font-bold uppercase tracking-wider">Tim Verifikator PUPR</span>
          </div>

          <a href="/" class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5">
            <i class="ph-bold ph-house text-sm"></i>
            <span>Portal Utama</span>
          </a>

          <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-3 py-1.5 rounded bg-red-950/80 border border-red-800 text-red-300 hover:bg-red-900 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 shadow-md">
              <i class="ph-bold ph-sign-out text-sm"></i>
              <span>Keluar</span>
            </button>
          </form>
        </div>
      </div>
    </header>

    <!-- CONTENT AREA DESKTOP -->
    <main class="max-w-7xl mx-auto px-6 py-8 flex-1 space-y-8">
      
      <!-- HERO BANNER PENGAWAS (PROMINEN GAMBAR HERO CONTRACTOR_HERO.JPG) -->
      <div class="relative rounded overflow-hidden border border-slate-800 bg-slate-950 shadow-2xl p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 min-h-[160px]">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/65 to-slate-950/30"></div>

        <div class="space-y-2 z-10">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-gov-900/80 border border-gov-750 text-emerald-300 text-[10px] font-bold uppercase tracking-wider backdrop-blur-sm">
            <i class="ph-bold ph-check-square-offset text-emerald-400"></i> Inspeksi & Kontrol Kualitas Fisik Dinas PUPR
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight font-heading uppercase">Dasbor Verifikasi Pengawas Lapangan</h2>
          <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
            Audit pengajuan progres dari kontraktor pelaksana, lakukan pemeriksaan foto bukti fisik, dan setujui atau tolak laporan sesuai spesifikasi teknis Dinas PUPR.
          </p>
        </div>

        <div class="flex items-center gap-3 z-10 shrink-0">
          <div class="bg-slate-950/90 border border-slate-800 p-4 rounded text-center min-w-[140px] shadow-lg">
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Butuh Review</span>
            <span class="text-2xl font-extrabold text-amber-400 font-heading">{{ $pendingProjects->count() }} Paket</span>
          </div>
        </div>
      </div>

      <!-- METRICS GRID DESKTOP -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-2 shadow-md">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-bold uppercase tracking-wider">Antrean Review Aktif</span>
            <i class="ph-bold ph-hourglass-high text-xl text-amber-400"></i>
          </div>
          <span class="text-2xl font-extrabold text-amber-400 block font-heading">{{ $pendingProjects->count() }} Paket</span>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-2 shadow-md">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-bold uppercase tracking-wider">Total Paket Dipantau</span>
            <i class="ph-bold ph-kanban text-xl text-gov-400"></i>
          </div>
          <span class="text-2xl font-extrabold text-white block font-heading">{{ $allProjects->count() }} Paket</span>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-2 shadow-md">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-bold uppercase tracking-wider">Capaian Progres Rata-rata</span>
            <i class="ph-bold ph-chart-line-up text-xl text-emerald-400"></i>
          </div>
          <span class="text-2xl font-extrabold text-emerald-400 block font-heading">{{ number_format($allProjects->avg('progress') ?? 0, 0) }}%</span>
        </div>
      </div>

      <!-- MAIN 2-COLUMN GRID DESKTOP -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT: ANTREAN VERIFIKASI LAPANGAN (7 COLS) -->
        <div class="lg:col-span-7 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2 font-heading">
              <i class="ph-bold ph-clock-counter-clockwise text-amber-400"></i> Antrean Verifikasi Progres Masuk
            </h3>
            <span class="text-xs text-slate-400 font-mono">Tercatat: {{ $pendingProjects->count() }} Permintaan</span>
          </div>

          <div class="space-y-4">
            @forelse($pendingProjects as $p)
              <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-4 shadow-lg">
                <div class="flex justify-between items-start gap-3">
                  <div>
                    <span class="text-[10px] font-extrabold text-gov-400 uppercase tracking-widest block">{{ $p->contractor->name }}</span>
                    <h4 class="text-base font-bold text-white leading-snug font-heading">{{ $p->nama_pekerjaan }}</h4>
                  </div>
                  <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-950 text-amber-300 border border-amber-800">
                    Pending Review
                  </span>
                </div>

                <!-- Foto & Details -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-950 p-3.5 rounded-xl border border-slate-850 items-center">
                  <div class="relative aspect-video sm:aspect-square rounded-lg overflow-hidden bg-slate-900 border border-slate-800">
                    <img src="{{ asset(ltrim($p->reported_photo ?? 'storage/projects/sample_default.jpg', '/')) }}" alt="Foto Progres" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='/storage/projects/sample_default.jpg';">
                  </div>
                  
                  <div class="sm:col-span-2 space-y-2 text-xs">
                    <div class="flex justify-between items-center text-slate-300">
                      <span>Progres Saat Ini:</span>
                      <span class="font-bold text-slate-400">{{ number_format($p->progress, 0) }}%</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-200">
                      <span class="font-bold">Usulan Progres Baru:</span>
                      <span class="font-extrabold text-gov-400 text-sm bg-gov-900/60 px-2 py-0.5 rounded border border-gov-750">{{ number_format($p->reported_progress, 0) }}%</span>
                    </div>
                    <span class="block text-[10px] text-slate-500 font-mono pt-1">
                      Diajukan pada: {{ $p->reported_at ? date('d M Y H:i', strtotime($p->reported_at)) : date('d M Y') }}
                    </span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-2">
                  <button type="button" onclick="openSupervisorReviewModal({{ $p->id }}, '{{ addslashes($p->nama_pekerjaan) }}', '{{ addslashes($p->contractor->name) }}', {{ $p->progress }}, {{ $p->reported_progress }}, 'reject')" class="flex-1 py-2.5 text-xs font-bold uppercase text-red-300 bg-red-950/40 hover:bg-red-950/80 border border-red-800/60 rounded-xl transition-all flex items-center justify-center gap-1.5">
                    <i class="ph-bold ph-x-circle text-base"></i>
                    <span>Tolak Laporan</span>
                  </button>
                  <button type="button" onclick="openSupervisorReviewModal({{ $p->id }}, '{{ addslashes($p->nama_pekerjaan) }}', '{{ addslashes($p->contractor->name) }}', {{ $p->progress }}, {{ $p->reported_progress }}, 'approve')" class="flex-1 py-2.5 text-xs font-bold uppercase text-white bg-gov-600 hover:bg-gov-500 rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5">
                    <i class="ph-bold ph-check-circle text-base"></i>
                    <span>Setujui Progres</span>
                  </button>
                </div>
              </div>
            @empty
              <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center text-slate-400 space-y-2">
                <i class="ph-bold ph-check-circle text-4xl text-emerald-500 block"></i>
                <p class="text-sm font-bold text-white">Antrean Bersih!</p>
                <p class="text-xs text-slate-400">Seluruh pengajuan progres fisik kontraktor telah selesai diverifikasi.</p>
              </div>
            @endforelse
          </div>
        </div>

        <!-- RIGHT: PETA LAPANGAN DESKTOP (5 COLS) -->
        <div class="lg:col-span-5 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-white flex items-center gap-2 font-heading">
              <i class="ph-bold ph-map-pin-line text-gov-400"></i> Sebaran Spasial Paket Pekerjaan
            </h3>
          </div>

          <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden p-2 shadow-lg">
            <div id="desktop-map" class="h-[420px] rounded-xl w-full"></div>
          </div>
        </div>

      </div>
    </main>
  </div>


  <!-- ========================================================= -->
  <!-- 2. MODE MOBILE NATIVE APP VIEW (Tampil pada Layar <768px) -->
  <!-- ========================================================= -->
  <div class="block md:hidden min-h-screen bg-slate-900 text-slate-100 font-sans flex flex-col justify-between">
    
    <!-- TOP MOBILE HEADER NOTCH BAR -->
    <div class="bg-slate-900/95 border-b border-slate-800 p-3.5 sticky top-0 z-30 shadow-md backdrop-blur-md">
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded bg-gov-600 flex items-center justify-center text-white font-bold shadow-sm">
            <i class="ph-bold ph-shield-check text-lg"></i>
          </div>
          <div>
            <div class="inline-flex items-center gap-1 text-[8px] font-bold tracking-wider uppercase text-slate-300">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>PEMKAB BANJARNEGARA</span>
            </div>
            <h1 class="text-xs font-extrabold text-white leading-none font-heading uppercase">SIBIJAK PENGAWAS LAPANGAN</h1>
          </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="w-8 h-8 rounded bg-red-950/80 border border-red-800 text-red-300 flex items-center justify-center shadow-md">
            <i class="ph-bold ph-sign-out text-sm"></i>
          </button>
        </form>
      </div>
    </div>

    <!-- MOBILE MAIN CONTENT TABS CONTAINER -->
    <main class="p-4 flex-1 overflow-y-auto space-y-4 pb-24">
      
      <!-- FLASH NOTIFICATION -->
      @if(session('success'))
        <div class="p-3 bg-emerald-950/80 border border-emerald-800 text-emerald-200 text-xs rounded flex items-center gap-2">
          <i class="ph-bold ph-check-circle text-base text-emerald-400 shrink-0"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      <!-- TAB 1: BERANDA PENGAWAS MOBILE (STYLE LANDING PAGE DESKTOP) -->
      <div id="m-sup-beranda" class="space-y-5">
        <!-- HERO BANNER PENGAWAS -->
        <div class="relative rounded overflow-hidden border border-slate-800 bg-slate-950 shadow-2xl p-5 space-y-3 min-h-[140px]">
          <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/30"></div>

          <div class="relative z-10 space-y-2">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-gov-900/90 border border-gov-750 text-emerald-300 text-[9px] font-bold uppercase tracking-wider backdrop-blur-sm">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>PENGAWAS LAPANGAN DINAS PUPR</span>
            </div>
            <h2 class="text-lg font-extrabold text-white leading-tight font-heading uppercase drop-shadow-md">
              PUSAT AUDIT &amp; VERIFIKASI PROGRES
            </h2>
            <p class="text-xs text-slate-200 leading-relaxed drop-shadow">
              Selamat datang, <strong class="text-white">{{ $user->name }}</strong>. Pantau antrean laporan progres fisik pekerjaan konstruksi dan inspeksi spasial Kabupaten Banjarnegara.
            </p>
          </div>
        </div>

        <!-- STAT CARDS MOBILE GRID 2x2 -->
        <div class="grid grid-cols-2 gap-3">
          <div onclick="switchSupervisorTab('verifikasi')" class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-1.5 cursor-pointer active:bg-slate-900 transition-all shadow-sm">
            <div class="flex items-center justify-between text-amber-400">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Butuh Review</span>
              <i class="ph-bold ph-clock-counter-clockwise text-base"></i>
            </div>
            <span class="text-xl font-extrabold text-amber-400 block font-heading">{{ $pendingProjects->count() }} Paket</span>
            <span class="text-[9px] text-slate-500 font-bold uppercase block">Antrean Lapangan</span>
          </div>

          <div class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-1.5 shadow-sm">
            <div class="flex items-center justify-between text-gov-400">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Proyek</span>
              <i class="ph-bold ph-briefcase text-base"></i>
            </div>
            <span class="text-xl font-extrabold text-white block font-heading">{{ $allProjects->count() }} Paket</span>
            <span class="text-[9px] text-slate-500 font-bold uppercase block">Pengawasan Fisik</span>
          </div>

          <div class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-1.5 shadow-sm">
            <div class="flex items-center justify-between text-emerald-400">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-rata Progres</span>
              <i class="ph-bold ph-chart-line-up text-base"></i>
            </div>
            <span class="text-xl font-extrabold text-emerald-400 block font-heading font-mono">{{ number_format($allProjects->avg('progress') ?? 0, 0) }}%</span>
            <span class="text-[9px] text-slate-500 font-bold uppercase block">Capaian Daerah</span>
          </div>

          <div class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-1.5 shadow-sm">
            <div class="flex items-center justify-between text-blue-400">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kontrak</span>
              <i class="ph-bold ph-currency-dollar-simple text-base"></i>
            </div>
            <span class="text-sm font-extrabold text-white block font-heading font-mono">Rp {{ number_format(($allProjects->sum('nilai_kontrak') ?? 0)/1000000000, 1) }} M</span>
            <span class="text-[9px] text-slate-500 font-bold uppercase block">Nilai Pengawasan</span>
          </div>
        </div>

        <!-- QUICK SHORTCUT BUTTONS -->
        <div class="grid grid-cols-2 gap-3">
          <button type="button" onclick="switchSupervisorTab('verifikasi')" class="p-3 bg-gov-600 hover:bg-gov-500 text-white rounded text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-md transition-all">
            <i class="ph-bold ph-check-square-offset text-base"></i>
            <span>Verifikasi ({{ $pendingProjects->count() }})</span>
          </button>
          <button type="button" onclick="switchSupervisorTab('peta')" class="p-3 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-md transition-all">
            <i class="ph-bold ph-map-trifold text-base"></i>
            <span>Peta Spasial</span>
          </button>
        </div>

        <!-- RINGKASAN PEKERJAAN TERBARU -->
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Paket Pekerjaan Pengawasan</h3>
            <span class="text-[10px] text-gov-400 font-mono font-bold">{{ $allProjects->count() }} Paket Active</span>
          </div>

          <div class="space-y-3">
            @foreach($allProjects->take(4) as $p)
              <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-3 shadow-sm">
                <div class="space-y-1">
                  <div class="flex justify-between items-start gap-2">
                    <span class="text-[8px] font-bold text-gov-400 uppercase tracking-widest">{{ $p->contractor->name }}</span>
                    <span class="text-[8px] font-bold uppercase px-2 py-0.5 rounded {{
                      $p->verification_status === 'pending' ? 'bg-amber-950 text-amber-300 border border-amber-800' : 'bg-slate-900 text-slate-400 border border-slate-800'
                    }}">
                      {{ $p->verification_status === 'pending' ? '⏳ Menunggu Review' : $p->status }}
                    </span>
                  </div>
                  <h4 class="text-xs font-bold text-white leading-snug font-heading uppercase">{{ $p->nama_pekerjaan }}</h4>
                </div>

                <div class="space-y-1">
                  <div class="flex justify-between text-[10px] font-bold text-slate-400">
                    <span>Progres Terverifikasi</span>
                    <span class="text-gov-400 font-mono">{{ number_format($p->progress, 0) }}%</span>
                  </div>
                  <div class="w-full bg-slate-900 h-1.5 rounded overflow-hidden border border-slate-800">
                    <div class="bg-gov-600 h-full rounded" style="width: {{ $p->progress }}%"></div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- TAB 2: ANTREAN VERIFIKASI MOBILE -->
      <div id="m-sup-verifikasi" class="hidden space-y-4">
        <div class="flex justify-between items-center">
          <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Antrean Verifikasi Progres</h2>
          <span class="text-[9px] bg-red-950 text-red-400 px-2 py-0.5 rounded border border-red-900 font-bold uppercase">{{ $pendingProjects->count() }} Butuh Review</span>
        </div>

        <div class="space-y-4">
          @forelse($pendingProjects as $p)
            <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-4 shadow-sm">
              <div class="space-y-1">
                <span class="block text-[8px] font-bold text-gov-400 uppercase tracking-widest">{{ $p->contractor->name }}</span>
                <h3 class="text-xs font-bold text-white leading-snug font-heading uppercase">{{ $p->nama_pekerjaan }}</h3>
                @if($p->pengawas_id === $user->id)
                  <span class="inline-flex items-center gap-1 text-[8px] font-bold text-emerald-400 uppercase tracking-wider bg-emerald-950/50 border border-emerald-900 rounded px-1.5 py-0.5">
                    <i class="ph-bold ph-user-focus"></i> Ditugaskan ke Anda
                  </span>
                @endif
              </div>

              <!-- Reported details with photo -->
              <div class="bg-slate-900 p-3 rounded border border-slate-800 space-y-3">
                <div class="relative aspect-video rounded overflow-hidden bg-slate-950 border border-slate-800">
                  <img src="{{ asset(ltrim($p->reported_photo ?? 'storage/projects/sample_default.jpg', '/')) }}" alt="Progress {{ $p->nama_pekerjaan }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='/storage/projects/sample_default.jpg';">
                </div>
                
                <div class="flex justify-between items-center text-xs">
                  <span class="text-slate-400 font-semibold">Usulan Progres:</span>
                  <span class="font-bold text-white text-sm bg-gov-900/60 px-2 py-0.5 rounded border border-gov-750 font-mono">{{ number_format($p->reported_progress, 0) }}%</span>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="flex gap-2">
                <button type="button" onclick="openSupervisorReviewModal({{ $p->id }}, '{{ addslashes($p->nama_pekerjaan) }}', '{{ addslashes($p->contractor->name) }}', {{ $p->progress }}, {{ $p->reported_progress }}, 'reject')" class="flex-1 py-2 text-[10px] font-bold uppercase tracking-wider text-red-400 bg-red-950/40 hover:bg-red-950/70 border border-red-900 rounded active:scale-[0.98] transition-all">
                  Tolak
                </button>
                <button type="button" onclick="openSupervisorReviewModal({{ $p->id }}, '{{ addslashes($p->nama_pekerjaan) }}', '{{ addslashes($p->contractor->name) }}', {{ $p->progress }}, {{ $p->reported_progress }}, 'approve')" class="flex-1 py-2 text-[10px] font-bold uppercase tracking-wider text-white bg-gov-600 hover:bg-gov-500 rounded active:scale-[0.98] transition-all shadow-sm">
                  Setujui
                </button>
              </div>
            </div>
          @empty
            <div class="text-center py-12 text-slate-500 text-xs font-semibold">
              <i class="ph-bold ph-check-circle text-3xl mb-2 block text-gov-600"></i>
              <span>Antrean bersih! Seluruh progres telah diverifikasi.</span>
            </div>
          @endforelse
        </div>
      </div>

      <!-- TAB 3: RIWAYAT VERIFIKASI MOBILE -->
      <div id="m-sup-riwayat" class="hidden space-y-4">
        <div class="flex justify-between items-center">
          <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Riwayat Verifikasi Saya</h2>
          <div class="flex gap-1.5">
            <span class="text-[9px] bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded border border-emerald-900 font-bold uppercase">{{ $jumlahDisetujui }} Setujui</span>
            <span class="text-[9px] bg-red-950 text-red-400 px-2 py-0.5 rounded border border-red-900 font-bold uppercase">{{ $jumlahDitolak }} Tolak</span>
          </div>
        </div>

        <div class="space-y-3">
          @forelse($riwayatVerifikasi as $log)
            <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-2 shadow-sm">
              <div class="flex justify-between items-start gap-2">
                <div class="space-y-0.5">
                  <span class="block text-[8px] font-bold text-slate-500 uppercase tracking-widest">{{ $log->project?->contractor?->name }}</span>
                  <h3 class="text-[11px] font-bold text-white leading-snug font-heading uppercase">{{ $log->project?->nama_pekerjaan }}</h3>
                </div>
                <span class="shrink-0 {{ $log->action === 'approve' ? 'bg-emerald-950/60 text-emerald-400 border-emerald-900' : 'bg-red-950/60 text-red-400 border-red-900' }} text-[8px] font-bold uppercase px-1.5 py-0.5 rounded border">
                  {{ $log->action === 'approve' ? 'Disetujui' : 'Ditolak' }} {{ number_format($log->progress, 0) }}%
                </span>
              </div>
              @if($log->photos->where('type', 'verification')->count())
                <div class="flex flex-wrap gap-1.5">
                  @foreach($log->photos->where('type', 'verification')->take(4) as $vPhoto)
                    <img src="{{ asset('storage/' . ltrim($vPhoto->path, '/')) }}" alt="{{ $vPhoto->caption }}" class="w-12 h-12 object-cover rounded border border-slate-800">
                  @endforeach
                  @if($log->photos->where('type', 'verification')->count() > 4)
                    <span class="w-12 h-12 rounded border border-slate-800 bg-slate-900 flex items-center justify-center text-[10px] font-bold text-slate-400">+{{ $log->photos->where('type', 'verification')->count() - 4 }}</span>
                  @endif
                </div>
              @endif
              @if($log->note)
                <p class="text-[10px] text-slate-500 italic leading-relaxed">"{{ \Illuminate\Support\Str::limit($log->note, 120) }}"</p>
              @endif
              <span class="block text-[9px] text-slate-600 font-mono">{{ date('d M Y H:i', strtotime($log->created_at)) }}</span>
            </div>
          @empty
            <div class="text-center py-12 text-slate-500 text-xs font-semibold">
              <i class="ph-bold ph-clock-counter-clockwise text-3xl mb-2 block text-gov-600"></i>
              <span>Belum ada riwayat verifikasi.</span>
            </div>
          @endforelse
        </div>
      </div>

      <!-- TAB 3: PETA LAPANGAN MOBILE -->
      <div id="m-sup-peta" class="hidden space-y-4">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Peta Sebaran Paket Proyek</h2>
        <div class="h-[380px] relative rounded overflow-hidden border border-slate-800">
          <div id="mobile-map" class="h-full w-full"></div>
        </div>
      </div>

      <!-- TAB 4: PROFIL PENGAWAS MOBILE -->
      <div id="m-sup-profil" class="hidden space-y-4">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Profil Pengawas Lapangan</h2>
        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-4 text-xs shadow-md">
          <div class="flex items-center gap-3 border-b border-slate-800 pb-4">
            <div class="w-10 h-10 rounded bg-gov-900 flex items-center justify-center text-white border border-gov-800 text-lg">
              <i class="ph-bold ph-user-check"></i>
            </div>
            <div>
              <h3 class="font-extrabold text-white text-sm font-heading uppercase">{{ $user->name }}</h3>
              <span class="text-[9px] text-gov-400 font-mono block">Dinas PUPR Kabupaten Banjarnegara</span>
            </div>
          </div>
          <div class="space-y-2.5 text-slate-300">
            <div>
              <span class="block text-slate-500 font-bold uppercase text-[9px]">Jabatan / Wewenang</span>
              <span class="font-bold text-white">Pengawas Lapangan{{ $user->bidang ? ' ' . $user->bidang : '' }}</span>
            </div>
            @if($user->nip)
              <div>
                <span class="block text-slate-500 font-bold uppercase text-[9px]">NIP</span>
                <span class="font-bold text-white font-mono">{{ $user->nip }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>

    </main>

    <!-- MOBILE STICKY BOTTOM NAVIGATION BAR -->
    <nav class="bg-slate-900/95 backdrop-blur-md border-t border-slate-800 fixed bottom-0 left-0 right-0 z-30 px-4 py-2">
      <div class="flex justify-around items-center max-w-md mx-auto">
        <button type="button" onclick="switchSupervisorTab('beranda')" id="btn-sup-beranda" class="flex flex-col items-center gap-1 text-gov-400 transition-all font-bold">
          <i class="ph-bold ph-house text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Beranda</span>
        </button>
        <button type="button" onclick="switchSupervisorTab('verifikasi')" id="btn-sup-verifikasi" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-check-square-offset text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Verifikasi</span>
        </button>
        <button type="button" onclick="switchSupervisorTab('riwayat')" id="btn-sup-riwayat" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-clock-counter-clockwise text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Riwayat</span>
        </button>
        <button type="button" onclick="switchSupervisorTab('peta')" id="btn-sup-peta" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-map-trifold text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Peta</span>
        </button>
        <button type="button" onclick="switchSupervisorTab('profil')" id="btn-sup-profil" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-user-circle text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Profil</span>
        </button>
      </div>
    </nav>
  </div>


  <!-- ========================================================= -->
  <!-- 3. POP-UP MODAL AUDIT REVIEW PENGAWAS (SHARED ALL VIEWS) -->
  <!-- ========================================================= -->
  <div id="supReviewModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-5 space-y-4 shadow-2xl animate-in fade-in duration-200">
      
      <div class="flex items-center justify-between border-b border-slate-800 pb-3">
        <div class="flex items-center gap-2">
          <span id="supReviewTypeBadge" class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-gov-600/20 text-gov-400 border border-gov-500/30">
            REVIEW AUDIT
          </span>
        </div>
        <button type="button" onclick="closeSupervisorReviewModal()" class="text-slate-400 hover:text-white">
          <i class="ph-bold ph-x text-lg"></i>
        </button>
      </div>

      <form id="supReviewForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
        @csrf
        <input type="hidden" name="action" id="supReviewActionInput">
        
        <div class="space-y-1">
          <label class="block font-bold text-slate-300">Paket Pekerjaan</label>
          <div id="supReviewProjectName" class="p-3 bg-slate-950 border border-slate-800 rounded-xl font-bold text-white"></div>
          <span id="supReviewContractorName" class="block text-[10px] text-gov-400 font-mono pt-0.5"></span>
        </div>

        <div class="p-3 bg-slate-950 border border-slate-800 rounded-xl flex justify-between items-center">
          <span class="text-slate-400">Perubahan Progres:</span>
          <span id="supReviewProgressText" class="font-extrabold text-gov-400 text-sm"></span>
        </div>

        <div class="space-y-1">
          <label for="supReviewNoteInput" class="block font-bold text-slate-300">Catatan / Alasan Hasil Inspeksi Pengawas (Lapisan 1 - jika disetujui, lanjut ke persetujuan akhir Admin PUPR)</label>
          <textarea name="note" id="supReviewNoteInput" rows="3" required placeholder="Tuliskan alasan hasil pemeriksaan lapangan secara jelas..." class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white focus:border-gov-600 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div class="space-y-1">
          <label for="supReviewPhotoInput" class="block font-bold text-slate-300">Foto Dokumentasi Lapangan (cross-check)</label>
          <input type="file" name="foto_dokumentasi[]" id="supReviewPhotoInput" multiple accept=".jpg,.jpeg,.png,.webp,image/*"
                 class="w-full text-[10px] text-slate-400 file:mr-2 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-gov-600 file:text-white file:font-bold file:text-[10px] hover:file:bg-gov-500 file:cursor-pointer bg-slate-950 border border-slate-800 rounded-xl p-2.5">
          <p class="text-[10px] text-slate-500 leading-relaxed">Unggah foto hasil inspeksi di lokasi untuk cross-check dengan dokumentasi penyedia jasa (maks. 8 foto, JPG/PNG/WebP @4 MB). Foto akan tersimpan sebagai dokumentasi pemeriksaan pada timeline proyek.</p>
          <div id="supReviewPhotoPreview" class="flex flex-wrap gap-2 pt-1"></div>
        </div>

        <div class="pt-2 flex gap-3">
          <button type="button" onclick="closeSupervisorReviewModal()" class="flex-1 py-2.5 text-xs font-bold uppercase text-slate-400 bg-slate-950 border border-slate-800 rounded-xl hover:bg-slate-800 transition-all">Batal</button>
          <button type="submit" id="supReviewSubmitBtn" class="flex-1 py-2.5 text-xs font-bold uppercase text-white bg-gov-600 hover:bg-gov-500 rounded-xl transition-all shadow-lg flex items-center justify-center gap-1.5">
            <span>Kirim Verifikasi</span>
          </button>
        </div>
      </form>
    </div>
  </div>


  <!-- SPATIAL LEAFLET MAP & TAB LOGIC -->
  <script>
    const projectsData = @json($allProjects);

    function initMap(mapId) {
      const container = document.getElementById(mapId);
      if (!container) return;

      const map = L.map(mapId).setView([-7.3986, 109.6974], 12);

      const streetMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; Esri &mdash; Esri, HERE, Garmin, USGS, Intermap, INCREMENT P, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
        maxZoom: 19
      }).addTo(map);

      const imageryMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; Esri &mdash; Esri, Maxar, Earthstar Geographics, and the GIS User Community',
        maxZoom: 19
      });

      const topoMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; Esri &mdash; Esri, HERE, Garmin, USGS, Intermap, INCREMENT P, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom',
        maxZoom: 19
      });

      // Layer grup overlay untuk titik proyek (bisa di-toggle)
      const projectLayer = L.layerGroup().addTo(map);

      L.control.layers({
        'Street Map': streetMap,
        'Citra Satelit': imageryMap,
        'Topografi': topoMap
      }, { 'Titik Proyek': projectLayer }, { position: 'topright' }).addTo(map);

      projectsData.forEach(p => {
        if (p.latitude && p.longitude) {
          const marker = L.marker([p.latitude, p.longitude]).addTo(projectLayer);
          marker.bindPopup(`
            <div style="font-family: 'Plus Jakarta Sans', sans-serif; padding: 4px;">
              <strong style="font-size: 12px; display: block; margin-bottom: 4px;">${p.nama_pekerjaan}</strong>
              <span style="font-size: 10px; color: #16a34a; font-weight: bold;">Progres: ${parseFloat(p.progress).toFixed(0)}%</span>
            </div>
          `);
        }
      });
    }

    document.addEventListener("DOMContentLoaded", function() {
      initMap('desktop-map');
      initMap('mobile-map');
    });

    function openSupervisorReviewModal(id, title, contractor, oldProgress, newProgress, action) {
      const form = document.getElementById('supReviewForm');
      const badge = document.getElementById('supReviewTypeBadge');
      const projectName = document.getElementById('supReviewProjectName');
      const contractorName = document.getElementById('supReviewContractorName');
      const progressText = document.getElementById('supReviewProgressText');
      const actionInput = document.getElementById('supReviewActionInput');
      const noteInput = document.getElementById('supReviewNoteInput');
      const submitBtn = document.getElementById('supReviewSubmitBtn');

      form.action = '/pengawas/verify/' + id;
      actionInput.value = action;
      projectName.innerText = title;
      contractorName.innerText = contractor;
      progressText.innerText = oldProgress.toFixed(0) + '% → ' + newProgress.toFixed(0) + '%';
      noteInput.value = '';
      document.getElementById('supReviewPhotoInput').value = '';
      document.getElementById('supReviewPhotoPreview').innerHTML = '';

      if (action === 'approve') {
        badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
        badge.innerText = 'SETUJUI (LAPISAN 1: PENGAWAS)';
        submitBtn.className = 'flex-1 py-2.5 text-xs font-bold uppercase text-white bg-gov-600 hover:bg-gov-500 rounded-xl transition-all shadow-lg flex items-center justify-center gap-1.5';
      } else {
        badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-red-500/20 text-red-400 border border-red-500/30';
        badge.innerText = 'TOLAK (LAPISAN 1: PENGAWAS)';
        submitBtn.className = 'flex-1 py-2.5 text-xs font-bold uppercase text-white bg-red-600 hover:bg-red-500 rounded-xl transition-all shadow-lg flex items-center justify-center gap-1.5';
      }

      document.getElementById('supReviewModal').classList.remove('hidden');
    }

    function closeSupervisorReviewModal() {
      document.getElementById('supReviewModal').classList.add('hidden');
    }

    // Preview foto dokumentasi yang dipilih
    document.getElementById('supReviewPhotoInput').addEventListener('change', function () {
      const preview = document.getElementById('supReviewPhotoPreview');
      preview.innerHTML = '';
      const maxPhotos = 8;
      const files = Array.from(this.files).slice(0, maxPhotos);

      if (this.files.length > maxPhotos) {
        alert('Maksimal ' + maxPhotos + ' foto. Hanya ' + maxPhotos + ' foto pertama yang akan diunggah.');
      }

      files.forEach(function (file) {
        if (!file.type.startsWith('image/')) return;
        const img = document.createElement('img');
        img.className = 'w-14 h-14 object-cover rounded-lg border border-slate-800';
        img.src = URL.createObjectURL(file);
        preview.appendChild(img);
      });
    });

    function switchSupervisorTab(tab) {
      ['beranda', 'verifikasi', 'riwayat', 'peta', 'profil'].forEach(t => {
        const el = document.getElementById('m-sup-' + t);
        const btn = document.getElementById('btn-sup-' + t);
        if (el && btn) {
          if (t === tab) {
            el.classList.remove('hidden');
            btn.className = 'flex flex-col items-center gap-1 text-gov-400 transition-all font-bold';
            if (t === 'peta') {
              setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 100);
            }
          } else {
            el.classList.add('hidden');
            btn.className = 'flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all';
          }
        }
      });
    }
  </script>
  <!-- FLOATING BACK TO TOP BUTTON -->
  <button id="supBackToTopBtn" type="button" onclick="scrollToTop()" class="fixed bottom-20 md:bottom-8 right-5 z-40 w-11 h-11 rounded-full bg-gov-600 hover:bg-gov-500 text-white border border-emerald-400/40 shadow-2xl flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none active:scale-95 group" title="Kembali ke atas">
    <i class="ph-bold ph-arrow-up text-lg group-hover:-translate-y-0.5 transition-transform"></i>
  </button>

  <script>
    const supBackToTopBtn = document.getElementById('supBackToTopBtn');
    if (supBackToTopBtn) {
      window.addEventListener('scroll', function() {
        if (window.scrollY > 250) {
          supBackToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
          supBackToTopBtn.classList.add('opacity-100');
        } else {
          supBackToTopBtn.classList.add('opacity-0', 'pointer-events-none');
          supBackToTopBtn.classList.remove('opacity-100');
        }
      });
    }

    function scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }
  </script>
</body>
</html>
