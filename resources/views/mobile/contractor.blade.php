<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Kontraktor - SIBIJAK Banjarnegara</title>
  
  <!-- Font and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
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
  </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen antialiased">
  
  <!-- ========================================================= -->
  <!-- 1. MODE DESKTOP / LAPTOP VIEW (Tampil pada Layar md:block >=768px) -->
  <!-- ========================================================= -->
  <div class="hidden md:flex flex-col min-h-screen">
    <!-- NAVBAR TOP DESKTOP -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-30 shadow-md">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded bg-gov-600 flex items-center justify-center text-white shadow-sm font-bold text-lg">
            <i class="ph-bold ph-hard-hat"></i>
          </div>
          <div>
            <div class="inline-flex items-center gap-1.5 text-[9px] font-bold tracking-wider uppercase text-slate-300">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>PEMERINTAH KABUPATEN BANJARNEGARA</span>
            </div>
            <h1 class="text-sm font-extrabold text-white leading-none font-heading uppercase">SIBIJAK KONTRAKTOR</h1>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-right hidden sm:block">
            <span class="block text-xs font-bold text-slate-200 leading-none font-heading uppercase">{{ $contractor->name ?? $user->name }}</span>
            <span class="text-[10px] text-gov-400 font-mono font-bold">NIB: {{ $contractor->nib ?? '31082026001' }}</span>
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
      
      <!-- HERO BANNER KONTRAKTOR -->
      <div class="relative rounded overflow-hidden border border-slate-800 bg-slate-950 shadow-2xl p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 min-h-[160px]">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/65 to-slate-950/30"></div>

        <div class="space-y-2 z-10">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-gov-900/80 border border-gov-750 text-emerald-300 text-[10px] font-bold uppercase tracking-wider backdrop-blur-sm">
            <i class="ph-bold ph-seal-check text-emerald-400"></i> Rekanan Terverifikasi Dinas PUPR
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight font-heading uppercase drop-shadow-md">{{ $contractor->name ?? 'PT Banjarnegara Prima Karya' }}</h2>
          <p class="text-xs sm:text-sm text-slate-200 max-w-2xl leading-relaxed drop-shadow">
            Selamat datang di Portal Penyedia Jasa. Pantau progres pekerjaan fisik, unggah bukti laporan lapangan, dan terima catatan inspeksi dari pengawas Dinas PUPR Banjarnegara.
          </p>
        </div>

        <div class="flex items-center gap-3 z-10 shrink-0">
          <div class="bg-slate-950/90 border border-slate-800 p-4 rounded text-center min-w-[140px] shadow-lg backdrop-blur-sm">
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Status Kualifikasi</span>
            <span class="text-xs font-extrabold text-gov-400 font-heading uppercase">Kualifikasi Menengah</span>
          </div>
        </div>
      </div>

      <!-- STAT CARDS DESKTOP -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-2 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-bold uppercase tracking-wider">Total Paket Pekerjaan</span>
            <i class="ph-bold ph-briefcase text-xl text-gov-400"></i>
          </div>
          <span class="text-2xl font-extrabold text-white block font-heading">{{ $projects->count() }} Paket</span>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-2 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-bold uppercase tracking-wider">Status Pengajuan</span>
            <i class="ph-bold ph-clock-counter-clockwise text-xl text-amber-400"></i>
          </div>
          <span class="text-2xl font-extrabold text-amber-400 block font-heading">
            {{ $projects->where('verification_status', 'pending')->count() }} Menunggu Review
          </span>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-2 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-bold uppercase tracking-wider">Progres Fisik Rata-rata</span>
            <i class="ph-bold ph-chart-line-up text-xl text-emerald-400"></i>
          </div>
          <span class="text-2xl font-extrabold text-emerald-400 block font-heading">
            {{ number_format($projects->avg('progress') ?? 0, 0) }}%
          </span>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-2 shadow-sm">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-xs font-bold uppercase tracking-wider">Nilai Total Kontrak</span>
            <i class="ph-bold ph-currency-dollar-simple text-xl text-blue-400"></i>
          </div>
          <span class="text-xl font-extrabold text-white block font-heading font-mono">
            Rp {{ number_format(($projects->sum('nilai_kontrak') ?? 0)/1000000000, 2) }} M
          </span>
        </div>
      </div>

      <!-- DAFTAR PEKERJAAN DESKTOP -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-base font-bold text-white flex items-center gap-2 font-heading uppercase tracking-wider">
            <i class="ph-bold ph-list-checks text-gov-400"></i> Daftar Paket Pekerjaan Aktif
          </h3>
          <span class="text-xs text-slate-400 font-mono font-bold">Tercatat: {{ $projects->count() }} Paket</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          @forelse($projects as $p)
            <div class="bg-slate-950 border border-slate-800 rounded p-6 space-y-5 shadow-lg flex flex-col justify-between">
              <div class="space-y-3">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <span class="text-[10px] font-bold text-gov-400 uppercase tracking-widest block">{{ $p->sub_kategori ?? 'Bina Marga' }}</span>
                    <h4 class="text-base font-bold text-white leading-snug font-heading uppercase">{{ $p->nama_pekerjaan }}</h4>
                  </div>
                  <span class="px-3 py-1 rounded text-[10px] font-extrabold uppercase tracking-wider shrink-0 {{
                    $p->verification_status === 'pending' ? 'bg-amber-950 text-amber-300 border border-amber-800' : 'bg-slate-900 text-slate-300 border border-slate-800'
                  }}">
                    @if($p->verification_status === 'pending')
                      <span class="inline-flex items-center gap-1"><i class="ph-bold ph-clock-counter-clockwise text-amber-400"></i> Menunggu Review</span>
                    @else
                      {{ $p->status }}
                    @endif
                  </span>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-2">
                  <div class="flex justify-between text-xs font-bold text-slate-300">
                    <span>CAPAIAN PROGRES FISIK</span>
                    <span class="text-gov-400 font-extrabold font-mono">{{ number_format($p->progress, 0) }}%</span>
                  </div>
                  <div class="w-full bg-slate-900 h-2.5 rounded overflow-hidden p-0.5 border border-slate-800">
                    <div class="bg-gov-600 h-full rounded transition-all duration-300" style="width: {{ $p->progress }}%"></div>
                  </div>
                </div>

                <!-- Latest Audit Log -->
                @if($p->verification_note)
                  @php
                    $isRejectNote = str_contains(strtolower($p->verification_note), 'ditolak');
                  @endphp
                  <div class="p-3 rounded border text-xs italic space-y-1 {{ $isRejectNote ? 'bg-red-950/40 border-red-900/50 text-red-300' : 'bg-emerald-950/40 border-emerald-900/50 text-emerald-300' }}">
                    <div class="flex items-center justify-between font-extrabold uppercase text-[9px] not-italic">
                      <span>Catatan Pengawas Lapangan PUPR:</span>
                      <span>{{ $isRejectNote ? '❌ Ditolak' : '✅ Disetujui' }}</span>
                    </div>
                    <p class="leading-relaxed">"{{ $p->verification_note }}"</p>
                  </div>
                @endif
              </div>

              <!-- Action Button -->
              <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                <a href="{{ route('pekerjaan.show', $p->id) }}" class="text-xs text-slate-400 hover:text-white font-bold flex items-center gap-1 uppercase tracking-wider">
                  <span>Lihat Detail Audit</span>
                  <i class="ph-bold ph-arrow-right"></i>
                </a>
                <button type="button" onclick="openContractorUploadModal({{ $p->id }}, '{{ addslashes($p->nama_pekerjaan) }}', {{ $p->progress }})" class="px-4 py-2 bg-gov-600 hover:bg-gov-500 text-white rounded text-xs font-bold uppercase tracking-wider shadow-md active:scale-[0.98] transition-all flex items-center gap-1.5">
                  <i class="ph-bold ph-upload-simple"></i>
                  <span>Upload Progres Baru</span>
                </button>
              </div>
            </div>
          @empty
            <div class="col-span-2 bg-slate-950 border border-slate-800 rounded p-12 text-center text-slate-400 space-y-2">
              <i class="ph-bold ph-folder-open text-4xl text-slate-600 block"></i>
              <p class="text-sm font-bold uppercase">Belum Ada Paket Pekerjaan Terdaftar</p>
            </div>
          @endforelse
        </div>
      </div>
    </main>
  </div>


  <!-- ========================================================= -->
  <!-- 2. MODE MOBILE NATIVE APP VIEW (Tampil pada Layar <768px) -->
  <!-- ========================================================= -->
  <div class="block md:hidden min-h-screen bg-slate-950 text-slate-100 font-sans flex flex-col justify-between">
    
    <!-- TOP MOBILE HEADER NOTCH BAR -->
    <div class="bg-slate-900 border-b border-slate-800 p-3.5 sticky top-0 z-30 shadow-md">
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded bg-gov-600 flex items-center justify-center text-white font-bold shadow-sm">
            <i class="ph-bold ph-hard-hat text-lg"></i>
          </div>
          <div>
            <div class="inline-flex items-center gap-1 text-[8px] font-bold tracking-wider uppercase text-slate-300">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>PEMKAB BANJARNEGARA</span>
            </div>
            <h1 class="text-xs font-extrabold text-white leading-none font-heading uppercase">SIBIJAK KONTRAKTOR</h1>
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

      <!-- TAB 1: BERANDA KONTRAKTOR MOBILE (STYLE LANDING PAGE DESKTOP) -->
      <div id="m-tab-beranda" class="space-y-5">
        <!-- Executive Hero Banner Header Card -->
        <div class="relative rounded overflow-hidden border border-slate-800 bg-slate-950 p-5 space-y-3 min-h-[145px] shadow-2xl">
          <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/30"></div>

          <div class="relative z-10 space-y-2">
            <div class="flex items-center justify-between gap-2">
              <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-gov-900/90 border border-gov-750 text-emerald-300 text-[9px] font-bold uppercase tracking-wider backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>PENYEDIA JASA KONSTRUKSI</span>
              </div>
              <span class="text-[9px] bg-gov-900/90 text-gov-300 px-2 py-0.5 rounded border border-gov-750 font-bold uppercase flex items-center gap-1 shrink-0 backdrop-blur-sm">
                <i class="ph-bold ph-seal-check text-emerald-400"></i> Terverifikasi
              </span>
            </div>

            <div>
              <h2 class="text-base sm:text-lg font-extrabold text-white leading-tight font-heading uppercase drop-shadow-md">
                PORTAL MITRA KONTRAKTOR PUPR
              </h2>
              <p class="text-xs text-slate-200 leading-relaxed drop-shadow">
                Selamat datang, <strong class="text-white">{{ $contractor->name ?? $user->name }}</strong>. Kelola laporan progres fisik lapangan dan pantau catatan inspeksi pengawas Dinas PUPR.
              </p>
            </div>
          </div>
        </div>

        <!-- STAT CARDS MOBILE GRID 2x2 -->
        <div class="grid grid-cols-2 gap-3">
          <div onclick="switchMobileTab('proyek')" class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-1.5 cursor-pointer active:bg-slate-900 transition-all shadow-sm">
            <div class="flex items-center justify-between text-gov-400">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Paket</span>
              <i class="ph-bold ph-briefcase text-base"></i>
            </div>
            <span class="text-xl font-extrabold text-white block font-heading">{{ $projects->count() }} Paket</span>
            <span class="text-[9px] text-slate-500 font-bold uppercase block">Pekerjaan Aktif</span>
          </div>

          <div onclick="switchMobileTab('proyek')" class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-1.5 cursor-pointer active:bg-slate-900 transition-all shadow-sm">
            <div class="flex items-center justify-between text-amber-400">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Menunggu Review</span>
              <i class="ph-bold ph-clock-counter-clockwise text-base"></i>
            </div>
            <span class="text-xl font-extrabold text-amber-400 block font-heading">
              {{ $projects->where('verification_status', 'pending')->count() }} Paket
            </span>
            <span class="text-[9px] text-slate-500 font-bold uppercase block">Status Pengajuan</span>
          </div>

          <div class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-1.5 shadow-sm">
            <div class="flex items-center justify-between text-emerald-400">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-rata Progres</span>
              <i class="ph-bold ph-chart-line-up text-base"></i>
            </div>
            <span class="text-xl font-extrabold text-emerald-400 block font-heading font-mono">
              {{ number_format($projects->avg('progress') ?? 0, 0) }}%
            </span>
            <span class="text-[9px] text-slate-500 font-bold uppercase block">Capaian Lapangan</span>
          </div>

          <div class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-1.5 shadow-sm">
            <div class="flex items-center justify-between text-blue-400">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kontrak</span>
              <i class="ph-bold ph-currency-dollar-simple text-base"></i>
            </div>
            <span class="text-xs font-extrabold text-white block font-heading font-mono">
              Rp {{ number_format(($projects->sum('nilai_kontrak') ?? 0)/1000000000, 2) }} M
            </span>
            <span class="text-[9px] text-slate-500 font-bold uppercase block">Nilai Pelaksanaan</span>
          </div>
        </div>

        <!-- QUICK ACTION SHORTCUTS -->
        <div class="grid grid-cols-2 gap-3">
          <button type="button" onclick="switchMobileTab('proyek')" class="p-3 bg-gov-600 hover:bg-gov-500 text-white rounded text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-md transition-all">
            <i class="ph-bold ph-list-checks text-base"></i>
            <span>Daftar Proyek ({{ $projects->count() }})</span>
          </button>
          <button type="button" onclick="switchMobileTab('profil')" class="p-3 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-md transition-all">
            <i class="ph-bold ph-user-circle text-base"></i>
            <span>Profil Contractor</span>
          </button>
        </div>

        <!-- RINGKASAN PAKET PEKERJAAN AKTIF -->
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Paket Pekerjaan Terdaftar</h3>
            <span class="text-[10px] text-gov-400 font-mono font-bold">{{ $projects->count() }} Paket</span>
          </div>

          <div class="space-y-3">
            @forelse($projects->take(3) as $p)
              <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-3 shadow-sm">
                <div class="space-y-1">
                  <div class="flex justify-between items-start gap-2">
                    <span class="text-[8px] font-bold text-gov-400 uppercase tracking-widest">{{ $p->sub_kategori ?? 'Bina Marga' }}</span>
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
                    <span>Progres Fisik Field</span>
                    <span class="text-gov-400 font-mono">{{ number_format($p->progress, 0) }}%</span>
                  </div>
                  <div class="w-full bg-slate-900 h-1.5 rounded overflow-hidden border border-slate-800">
                    <div class="bg-gov-600 h-full rounded" style="width: {{ $p->progress }}%"></div>
                  </div>
                </div>
              </div>
            @empty
              <div class="text-center py-8 text-slate-500 text-xs font-semibold">Belum ada paket pekerjaan.</div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- TAB 2: DAFTAR PROYEK KONTRAKTOR -->
      <div id="m-tab-proyek" class="hidden space-y-4">
        <div class="flex justify-between items-center">
          <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Paket Pekerjaan Anda</h2>
          <span class="text-[10px] bg-slate-950 border border-slate-800 px-2 py-0.5 rounded text-slate-300 font-mono font-bold">{{ $projects->count() }} Paket</span>
        </div>

        <div class="space-y-4">
          @forelse($projects as $p)
            <div class="bg-slate-950 border border-slate-800 p-4 rounded space-y-4 shadow-md">
              <div class="space-y-1">
                <div class="flex justify-between items-start gap-2">
                  <span class="text-[8px] font-bold text-gov-400 uppercase tracking-widest">{{ $p->sub_kategori ?? 'Bina Marga' }}</span>
                  <span class="text-[8px] font-bold uppercase px-2 py-0.5 rounded {{
                    $p->verification_status === 'pending' ? 'bg-amber-950 text-amber-300 border border-amber-800' : 'bg-slate-900 text-slate-400 border border-slate-800'
                  }}">
                    {{ $p->verification_status === 'pending' ? '⏳ Menunggu Review' : $p->status }}
                  </span>
                </div>
                <h3 class="text-xs font-bold text-white leading-snug font-heading uppercase">{{ $p->nama_pekerjaan }}</h3>
              </div>

              <!-- Progress bar -->
              <div class="space-y-1.5">
                <div class="flex justify-between text-[10px] font-bold text-slate-400">
                  <span>Progres Fisik Field</span>
                  <span class="text-gov-400 font-extrabold font-mono">{{ number_format($p->progress, 0) }}%</span>
                </div>
                <div class="w-full bg-slate-900 h-2 rounded overflow-hidden border border-slate-800">
                  <div class="bg-gov-600 h-full rounded" style="width: {{ $p->progress }}%"></div>
                </div>
              </div>

              <!-- Audit note if present -->
              @if($p->verification_note)
                @php
                  $isReject = str_contains(strtolower($p->verification_note), 'ditolak');
                @endphp
                <div class="p-2.5 rounded border text-[11px] italic space-y-0.5 {{ $isReject ? 'bg-red-950/40 border-red-900/50 text-red-300' : 'bg-emerald-950/40 border-emerald-900/50 text-emerald-300' }}">
                  <span class="block font-bold not-italic text-[9px] uppercase">Catatan Pengawas Lapangan:</span>
                  <p class="leading-relaxed">"{{ $p->verification_note }}"</p>
                </div>
              @endif

              <button type="button" onclick="openContractorUploadModal({{ $p->id }}, '{{ addslashes($p->nama_pekerjaan) }}', {{ $p->progress }})" class="w-full py-2.5 text-xs font-bold uppercase tracking-wider text-white bg-gov-600 hover:bg-gov-500 rounded active:scale-[0.98] transition-all flex items-center justify-center gap-1.5 shadow-md">
                <i class="ph-bold ph-upload-simple"></i>
                <span>Upload Progres Baru</span>
              </button>
            </div>
          @empty
            <div class="text-center py-12 text-slate-500 text-xs font-semibold space-y-1">
              <i class="ph-bold ph-folder-open text-3xl mb-1 block text-slate-600"></i>
              <span>Belum ada paket pekerjaan fisik.</span>
            </div>
          @endforelse
        </div>
      </div>

      <!-- TAB 3: LAPOR PENGGUNAAN PROGRES QUICK TAB -->
      <div id="m-tab-lapor" class="hidden space-y-4">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Pilih Paket Untuk Dilaporkan</h2>
        <div class="space-y-3">
          @foreach($projects as $p)
            <div onclick="openContractorUploadModal({{ $p->id }}, '{{ addslashes($p->nama_pekerjaan) }}', {{ $p->progress }})" class="bg-slate-950 border border-slate-800 p-4 rounded flex items-center justify-between cursor-pointer active:bg-slate-900 transition-all">
              <div class="space-y-1 pr-3">
                <h4 class="text-xs font-bold text-white leading-snug font-heading uppercase">{{ $p->nama_pekerjaan }}</h4>
                <span class="text-[10px] text-gov-400 font-bold font-mono block">Capaian: {{ number_format($p->progress, 0) }}%</span>
              </div>
              <div class="w-8 h-8 rounded bg-gov-600/20 text-gov-400 border border-gov-500/30 flex items-center justify-center shrink-0">
                <i class="ph-bold ph-plus text-base"></i>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- TAB 4: PROFIL KONTRAKTOR -->
      <div id="m-tab-profil" class="hidden space-y-4">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-heading">Profil Penyedia Jasa</h2>
        
        <div class="bg-slate-950 border border-slate-800 p-5 rounded space-y-4 text-xs shadow-md">
          <div class="flex items-center gap-3 border-b border-slate-800 pb-4">
            <div class="w-12 h-12 rounded bg-gov-900 flex items-center justify-center text-white border border-gov-800 text-xl font-bold">
              <i class="ph-bold ph-buildings"></i>
            </div>
            <div>
              <h3 class="font-extrabold text-white text-sm leading-tight font-heading uppercase">{{ $contractor->name ?? $user->name }}</h3>
              <span class="text-[10px] text-gov-400 font-mono font-bold">NIB: {{ $contractor->nib ?? '31082026001' }}</span>
            </div>
          </div>

          <div class="space-y-3 text-slate-300">
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Pimpinan / Direktur</span>
              <span class="font-bold text-white">{{ $contractor->pimpinan ?? 'H. Ahmad Subarkah, ST' }}</span>
            </div>
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Alamat Kantor</span>
              <span class="text-slate-300 leading-relaxed block">{{ $contractor->alamat ?? 'Jl. Pemuda No. 45, Banjarnegara' }}</span>
            </div>
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Kualifikasi Registrasi</span>
              <span class="inline-block px-2.5 py-0.5 rounded bg-gov-950 border border-gov-800 text-[10px] font-bold text-gov-300 uppercase mt-1">
                Kualifikasi Menengah (Dinas PUPR)
              </span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- MOBILE STICKY BOTTOM NAVIGATION BAR -->
    <nav class="bg-slate-900/95 backdrop-blur-md border-t border-slate-800 fixed bottom-0 left-0 right-0 z-30 px-4 py-2">
      <div class="flex justify-around items-center max-w-md mx-auto">
        <button type="button" onclick="switchMobileTab('beranda')" id="btn-m-beranda" class="flex flex-col items-center gap-1 text-gov-400 transition-all font-bold">
          <i class="ph-bold ph-house text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Beranda</span>
        </button>
        <button type="button" onclick="switchMobileTab('proyek')" id="btn-m-proyek" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-hard-hat text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Proyek</span>
        </button>
        <button type="button" onclick="switchMobileTab('lapor')" id="btn-m-lapor" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-upload-simple text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Lapor</span>
        </button>
        <button type="button" onclick="switchMobileTab('profil')" id="btn-m-profil" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all">
          <i class="ph-bold ph-user-circle text-xl"></i>
          <span class="text-[9px] font-bold uppercase">Profil</span>
        </button>
      </div>
    </nav>
  </div>


  <!-- ========================================================= -->
  <!-- 3. POP-UP MODAL DIALOG UPLOAD PROGRES (SHARED ALL VIEWS) -->
  <!-- ========================================================= -->
  <div id="ctrUploadModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-5 space-y-4 shadow-2xl animate-in fade-in duration-200">
      
      <div class="flex items-center justify-between border-b border-slate-800 pb-3">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-gov-600/20 border border-gov-500/30 text-gov-400 flex items-center justify-center font-bold">
            <i class="ph-bold ph-upload-simple text-lg"></i>
          </div>
          <h3 class="text-sm font-bold text-white font-heading">Form Laporkan Progres Fisik</h3>
        </div>
        <button type="button" onclick="closeContractorUploadModal()" class="text-slate-400 hover:text-white">
          <i class="ph-bold ph-x text-lg"></i>
        </button>
      </div>

      <form id="ctrUploadForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
        @csrf
        
        <div class="space-y-1">
          <label class="block font-bold text-slate-300">Nama Paket Pekerjaan</label>
          <div id="ctrModalProjectName" class="p-3 bg-slate-950 border border-slate-800 rounded-xl font-bold text-gov-400"></div>
          <span id="ctrModalCurrentProgress" class="block text-[10px] text-slate-400 font-mono pt-0.5"></span>
        </div>

        <div class="space-y-1">
          <label for="ctrModalProgressInput" class="block font-bold text-slate-300">Usulan Persentase Progres Baru (%)</label>
          <input type="number" step="0.1" min="0" max="100" name="progress" id="ctrModalProgressInput" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white font-bold focus:border-gov-600 focus:outline-none text-sm">
        </div>

        <div class="space-y-1">
          <label for="ctrModalPhotoInput" class="block font-bold text-slate-300">Unggah Dokumentasi Foto Fisik Lapangan</label>
          <input type="file" name="photo" id="ctrModalPhotoInput" accept="image/*" required class="w-full text-slate-400 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gov-600 file:text-white hover:file:bg-gov-500 cursor-pointer bg-slate-950 border border-slate-800 rounded-xl py-1">
          <span class="block text-[10px] text-slate-400 mt-1">Format: JPG, PNG. Maksimal 2MB.</span>
        </div>

        <div class="pt-2 flex gap-3">
          <button type="button" onclick="closeContractorUploadModal()" class="flex-1 py-2.5 text-xs font-bold uppercase text-slate-400 bg-slate-950 border border-slate-800 rounded-xl hover:bg-slate-800 transition-all">Batal</button>
          <button type="submit" class="flex-1 py-2.5 text-xs font-bold uppercase text-white bg-gov-600 hover:bg-gov-500 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
            <i class="ph-bold ph-paper-plane-right text-base"></i>
            <span>Kirim Laporan</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- JAVASCRIPT TAB SWITCHER & MODAL LOGIC -->
  <script>
    function openContractorUploadModal(id, title, progress) {
      document.getElementById('ctrUploadForm').action = '/kontraktor/report/' + id;
      document.getElementById('ctrModalProjectName').innerText = title;
      document.getElementById('ctrModalCurrentProgress').innerText = 'Capaian Fisik Saat Ini: ' + parseFloat(progress).toFixed(0) + '%';
      document.getElementById('ctrModalProgressInput').value = progress;
      document.getElementById('ctrUploadModal').classList.remove('hidden');
    }

    function closeContractorUploadModal() {
      document.getElementById('ctrUploadModal').classList.add('hidden');
    }

    function switchMobileTab(tab) {
      ['beranda', 'proyek', 'lapor', 'profil'].forEach(t => {
        const el = document.getElementById('m-tab-' + t);
        const btn = document.getElementById('btn-m-' + t);
        if (el && btn) {
          if (t === tab) {
            el.classList.remove('hidden');
            btn.className = 'flex flex-col items-center gap-1 text-gov-400 transition-all font-bold';
          } else {
            el.classList.add('hidden');
            btn.className = 'flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-all';
          }
        }
      });
    }
  </script>
  <!-- FLOATING BACK TO TOP BUTTON -->
  <button id="ctrBackToTopBtn" type="button" onclick="scrollToTop()" class="fixed bottom-20 md:bottom-8 right-5 z-40 w-11 h-11 rounded-full bg-gov-600 hover:bg-gov-500 text-white border border-emerald-400/40 shadow-2xl flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none active:scale-95 group" title="Kembali ke atas">
    <i class="ph-bold ph-arrow-up text-lg group-hover:-translate-y-0.5 transition-transform"></i>
  </button>

  <script>
    const ctrBackToTopBtn = document.getElementById('ctrBackToTopBtn');
    if (ctrBackToTopBtn) {
      window.addEventListener('scroll', function() {
        if (window.scrollY > 250) {
          ctrBackToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
          ctrBackToTopBtn.classList.add('opacity-100');
        } else {
          ctrBackToTopBtn.classList.add('opacity-0', 'pointer-events-none');
          ctrBackToTopBtn.classList.remove('opacity-100');
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
