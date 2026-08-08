<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIKAP Mobile - Contractor</title>
  
  <!-- Font and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            gov: {
              50: '#f0fdf4',
              100: '#dcfce7',
              600: '#16a34a',
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
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-0 sm:p-4">
  
  <!-- Mobile Mockup Wrapper Frame -->
  <div class="w-full max-w-md bg-slate-950 border border-slate-800 text-slate-100 sm:rounded-[36px] shadow-2xl relative flex flex-col h-screen sm:h-[840px] overflow-hidden">
    
    <!-- Top Notch/Header -->
    <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex-shrink-0 flex justify-between items-center z-20">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 rounded bg-gov-600 flex items-center justify-center text-white text-xs">
          <i class="ph-bold ph-hard-hat"></i>
        </div>
        <span class="text-xs font-bold uppercase tracking-wider text-white">SIKAP Mobile (Pelaksana)</span>
      </div>
      
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-slate-400 hover:text-white" title="Keluar">
          <i class="ph-bold ph-sign-out text-lg"></i>
        </button>
      </form>
    </header>

    <!-- CONTENT SCROLLER -->
    <div class="flex-1 overflow-y-auto px-5 py-6 pb-24 space-y-6">
      
      @if(session('success'))
        <div class="bg-emerald-950/80 border border-emerald-800/80 text-emerald-400 p-4 rounded-xl text-xs font-semibold">
          {{ session('success') }}
        </div>
      @endif

      <!-- TAB 1: PROYEK SAYA -->
      <div id="tab-proyek" class="space-y-4">
        <div class="flex justify-between items-center">
          <h2 class="text-sm font-bold text-white uppercase tracking-wider">Pekerjaan Aktif</h2>
          <span class="text-[9px] bg-slate-850 px-2 py-0.5 rounded text-slate-400 font-mono">{{ $projects->count() }} Paket</span>
        </div>

        <div class="space-y-4">
          @forelse($projects as $p)
            <div class="bg-slate-900 border border-slate-800/60 p-4 rounded-2xl space-y-3">
              <div class="flex justify-between items-start gap-3">
                <h3 class="text-xs font-bold text-white leading-snug">{{ $p->nama_pekerjaan }}</h3>
                <span class="flex-shrink-0 inline-block px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider {{
                  $p->verification_status === 'pending' ? 'bg-amber-950 text-amber-400 border border-amber-800' : (
                    $p->status === 'Selesai' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-slate-800 text-slate-400 border border-slate-700'
                  )
                }}">
                  {{ $p->verification_status === 'pending' ? 'Menunggu Review' : $p->status }}
                </span>
              </div>

              <div class="space-y-2">
                <div class="flex justify-between text-[10px] font-bold text-slate-400">
                  <span>PROGRESS SEKARANG</span>
                  <span>{{ number_format($p->progress, 0) }}%</span>
                </div>
                <div class="w-full bg-slate-950 h-1.5 rounded-full overflow-hidden">
                  <div class="bg-gov-600 h-1.5 rounded-full" style="width: {{ $p->progress }}%"></div>
                </div>
              </div>

              <!-- Quick action to show upload form -->
              @if($p->verification_status !== 'pending' && $p->status !== 'Selesai')
                <button onclick="showReportForm({{ json_encode($p) }})" class="w-full py-2 bg-gov-600 hover:bg-gov-700 active:scale-[0.98] rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all">
                  Laporkan Progres Baru
                </button>
              @endif
            </div>
          @empty
            <div class="text-center py-12 text-slate-500 text-xs font-semibold">
              <i class="ph-bold ph-hard-hat text-3xl mb-2 block"></i>
              <span>Belum ada paket pekerjaan terdaftar.</span>
            </div>
          @endforelse
        </div>
      </div>

      <!-- TAB 2: LAPOR PROGRESS FORM -->
      <div id="tab-lapor" class="hidden space-y-4">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Form Pelaporan Progres</h2>
        
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl">
          <form id="form-report" action="" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Pekerjaan</label>
              <input type="text" id="report-p-name" disabled class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-850 rounded-xl text-slate-400 focus:outline-none">
            </div>

            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Ajukan Capaian Fisik (%)</label>
              <input type="number" step="0.01" name="progress" id="report-p-progress" min="0" max="100" required class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-slate-850 rounded-xl text-white focus:outline-none focus:ring-1 focus:ring-gov-600">
            </div>

            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Unggah Foto Dokumentasi Lapangan</label>
              <div class="relative">
                <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-slate-950 file:text-white file:hover:bg-slate-900 cursor-pointer">
              </div>
              <span class="block text-[8px] text-slate-500 mt-2">Format: JPG, PNG. Maksimal 2MB.</span>
            </div>

            <div class="pt-4 flex gap-2">
              <button type="button" onclick="switchMobileTab('proyek')" class="flex-1 py-2.5 text-[10px] font-bold uppercase text-slate-400 bg-slate-950 border border-slate-800 rounded-xl active:scale-[0.98]">Batal</button>
              <button type="submit" class="flex-1 py-2.5 text-[10px] font-bold uppercase text-white bg-gov-600 hover:bg-gov-700 rounded-xl active:scale-[0.98]">Kirim Laporan</button>
            </div>
          </form>
        </div>
      </div>

      <!-- TAB 3: PROFIL KONTRAKTOR -->
      <div id="tab-profil" class="hidden space-y-4">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Profil Badan Usaha</h2>
        
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-5 text-xs">
          <div class="flex items-center gap-3 border-b border-slate-800 pb-4">
            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white border border-slate-700 text-lg">
              <i class="ph-bold ph-buildings"></i>
            </div>
            <div>
              <h3 class="font-bold text-white leading-none">{{ $contractor->name }}</h3>
              <span class="text-[9px] text-slate-500 tracking-wide font-mono mt-1 block">NIB: {{ $contractor->nib }}</span>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-1">Penanggung Jawab</span>
              <span class="font-bold text-slate-300">{{ $contractor->pj }}</span>
            </div>
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-1">Kualifikasi Usaha</span>
              <span class="inline-block px-2 py-0.5 rounded bg-slate-950 border border-slate-850 text-[9px] font-bold text-slate-400 uppercase">
                {{ $contractor->kualifikasi }}
              </span>
            </div>
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-1">Alamat Kantor</span>
              <span class="text-slate-400 font-medium leading-relaxed">{{ $contractor->alamat }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- BOTTOM NAVIGATION MENU -->
    <nav class="absolute bottom-0 inset-x-0 bg-slate-900 border-t border-slate-800 h-16 flex items-center justify-around z-20">
      <button onclick="switchMobileTab('proyek')" id="nav-proyek" class="flex flex-col items-center gap-1 text-gov-600 transition-colors">
        <i class="ph-bold ph-layout text-lg"></i>
        <span class="text-[9px] font-bold uppercase tracking-wider">Proyek</span>
      </button>
      <button onclick="switchMobileTab('lapor')" id="nav-lapor" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors">
        <i class="ph-bold ph-camera text-lg"></i>
        <span class="text-[9px] font-bold uppercase tracking-wider">Lapor</span>
      </button>
      <button onclick="switchMobileTab('profil')" id="nav-profil" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors">
        <i class="ph-bold ph-user-circle text-lg"></i>
        <span class="text-[9px] font-bold uppercase tracking-wider">Profil</span>
      </button>
    </nav>

  </div>

  <script>
    function switchMobileTab(tabName) {
      // Hide all tabs
      document.getElementById('tab-proyek').classList.add('hidden');
      document.getElementById('tab-lapor').classList.add('hidden');
      document.getElementById('tab-profil').classList.add('hidden');

      // Reset nav button styling
      document.getElementById('nav-proyek').className = "flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors";
      document.getElementById('nav-lapor').className = "flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors";
      document.getElementById('nav-profil').className = "flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors";

      // Show requested tab & highlight button
      if (tabName === 'proyek') {
        document.getElementById('tab-proyek').classList.remove('hidden');
        document.getElementById('nav-proyek').className = "flex flex-col items-center gap-1 text-gov-600 transition-colors";
      } else if (tabName === 'lapor') {
        document.getElementById('tab-lapor').classList.remove('hidden');
        document.getElementById('nav-lapor').className = "flex flex-col items-center gap-1 text-gov-600 transition-colors";
      } else if (tabName === 'profil') {
        document.getElementById('tab-profil').classList.remove('hidden');
        document.getElementById('nav-profil').className = "flex flex-col items-center gap-1 text-gov-600 transition-colors";
      }
    }

    function showReportForm(p) {
      document.getElementById('report-p-name').value = p.nama_pekerjaan;
      document.getElementById('report-p-progress').value = p.progress;
      document.getElementById('form-report').action = `/mobile/kontraktor/report/${p.id}`;
      switchMobileTab('lapor');
    }
  </script>
</body>
</html>
