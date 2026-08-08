<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIKAP Mobile - Pengawas</title>
  
  <!-- Font and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Leaflet CSS & JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  
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
  <style>
    #mobile-map {
      height: 100%;
      width: 100%;
      border-radius: 16px;
    }
  </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-0 sm:p-4">
  
  <!-- Mobile Mockup Wrapper Frame -->
  <div class="w-full max-w-md bg-slate-950 border border-slate-800 text-slate-100 sm:rounded-[36px] shadow-2xl relative flex flex-col h-screen sm:h-[840px] overflow-hidden">
    
    <!-- Top Notch/Header -->
    <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex-shrink-0 flex justify-between items-center z-20">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 rounded bg-gov-600 flex items-center justify-center text-white text-xs">
          <i class="ph-bold ph-shield-checkered"></i>
        </div>
        <span class="text-xs font-bold uppercase tracking-wider text-white">SIKAP Mobile (Pengawas)</span>
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

      <!-- TAB 1: VERIFIKASI PROGRESS -->
      <div id="tab-verifikasi" class="space-y-4">
        <div class="flex justify-between items-center">
          <h2 class="text-sm font-bold text-white uppercase tracking-wider">Antrean Verifikasi</h2>
          <span class="text-[9px] bg-red-950 text-red-400 px-2 py-0.5 rounded border border-red-900 font-bold uppercase tracking-wider">
            {{ $pendingProjects->count() }} Butuh Review
          </span>
        </div>

        <div class="space-y-4">
          @forelse($pendingProjects as $p)
            <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl space-y-4">
              <div class="space-y-1">
                <span class="block text-[8px] font-bold text-gov-600 uppercase tracking-widest">{{ $p->contractor->name }}</span>
                <h3 class="text-xs font-bold text-white leading-snug">{{ $p->nama_pekerjaan }}</h3>
              </div>

              <!-- Reported details with photo -->
              <div class="bg-slate-950 p-3 rounded-xl border border-slate-850 space-y-3">
                <div class="relative aspect-video rounded-lg overflow-hidden bg-slate-900">
                  <img src="{{ $p->reported_photo }}" alt="Progress {{ $p->nama_pekerjaan }}" class="w-full h-full object-cover">
                </div>
                
                <div class="flex justify-between items-center text-xs">
                  <span class="text-slate-400 font-semibold">Progress Diajukan:</span>
                  <span class="font-bold text-white text-sm bg-gov-900/60 px-2 py-0.5 rounded">{{ number_format($p->reported_progress, 1) }}%</span>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="flex gap-2">
                <form action="{{ route('mobile.pengawas.verify', $p) }}" method="POST" class="flex-1">
                  @csrf
                  <input type="hidden" name="action" value="reject">
                  <button type="submit" class="w-full py-2 text-[9px] font-bold uppercase text-red-400 bg-red-950/20 hover:bg-red-950/40 border border-red-900/40 rounded-xl active:scale-[0.98]">
                    Tolak
                  </button>
                </form>
                <form action="{{ route('mobile.pengawas.verify', $p) }}" method="POST" class="flex-1">
                  @csrf
                  <input type="hidden" name="action" value="approve">
                  <button type="submit" class="w-full py-2 text-[9px] font-bold uppercase text-white bg-gov-600 hover:bg-gov-750 rounded-xl active:scale-[0.98] shadow-sm">
                    Setujui
                  </button>
                </form>
              </div>
            </div>
          @empty
            <div class="text-center py-16 text-slate-500 text-xs font-semibold">
              <i class="ph-bold ph-check-circle text-3xl mb-2 block text-gov-600"></i>
              <span>Antrean bersih! Seluruh progres pengajuan telah diverifikasi.</span>
            </div>
          @endforelse
        </div>
      </div>

      <!-- TAB 2: PETA LAPANGAN -->
      <div id="tab-peta" class="hidden flex flex-col h-full space-y-4">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Peta Sebaran Paket Proyek</h2>
        <div class="flex-1 min-h-[350px] relative rounded-2xl overflow-hidden border border-slate-800">
          <div id="mobile-map"></div>
        </div>
      </div>

      <!-- TAB 3: PROFIL PENGAWAS -->
      <div id="tab-profil" class="hidden space-y-4">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Profil Pengawas</h2>
        
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-5 text-xs">
          <div class="flex items-center gap-3 border-b border-slate-800 pb-4">
            <div class="w-10 h-10 rounded-full bg-gov-900 flex items-center justify-center text-white border border-gov-800 text-lg">
              <i class="ph-bold ph-user"></i>
            </div>
            <div>
              <h3 class="font-bold text-white leading-none">{{ $user->name }}</h3>
              <span class="text-[9px] text-slate-500 tracking-wide font-mono mt-1 block">ID: {{ $user->id }}</span>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-1">Instansi</span>
              <span class="font-bold text-slate-300">Dinas Pekerjaan Umum & Penataan Ruang (PUPR)</span>
            </div>
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-1">Wewenang / Hak Akses</span>
              <span class="inline-block px-2 py-0.5 rounded bg-slate-950 border border-slate-850 text-[9px] font-bold text-gov-400 uppercase">
                Field Examiner (Pemeriksa Progress)
              </span>
            </div>
            <div>
              <span class="block text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-1">Kabupaten</span>
              <span class="font-bold text-slate-300">Banjarnegara, Jawa Tengah</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- BOTTOM NAVIGATION MENU -->
    <nav class="absolute bottom-0 inset-x-0 bg-slate-900 border-t border-slate-800 h-16 flex items-center justify-around z-20">
      <button onclick="switchMobileTab('verifikasi')" id="nav-verifikasi" class="flex flex-col items-center gap-1 text-gov-600 transition-colors">
        <i class="ph-bold ph-list-checks text-lg"></i>
        <span class="text-[9px] font-bold uppercase tracking-wider">Verifikasi</span>
      </button>
      <button onclick="switchMobileTab('peta')" id="nav-peta" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors">
        <i class="ph-bold ph-map-pin text-lg"></i>
        <span class="text-[9px] font-bold uppercase tracking-wider">Peta</span>
      </button>
      <button onclick="switchMobileTab('profil')" id="nav-profil" class="flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors">
        <i class="ph-bold ph-user-circle text-lg"></i>
        <span class="text-[9px] font-bold uppercase tracking-wider">Profil</span>
      </button>
    </nav>

  </div>

  <script>
    let mapLoaded = false;
    let mobileMap = null;

    function switchMobileTab(tabName) {
      document.getElementById('tab-verifikasi').classList.add('hidden');
      document.getElementById('tab-peta').classList.add('hidden');
      document.getElementById('tab-profil').classList.add('hidden');

      document.getElementById('nav-verifikasi').className = "flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors";
      document.getElementById('nav-peta').className = "flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors";
      document.getElementById('nav-profil').className = "flex flex-col items-center gap-1 text-slate-500 hover:text-white transition-colors";

      if (tabName === 'verifikasi') {
        document.getElementById('tab-verifikasi').classList.remove('hidden');
        document.getElementById('nav-verifikasi').className = "flex flex-col items-center gap-1 text-gov-600 transition-colors";
      } else if (tabName === 'peta') {
        document.getElementById('tab-peta').classList.remove('hidden');
        document.getElementById('nav-peta').className = "flex flex-col items-center gap-1 text-gov-600 transition-colors";
        
        // Lazy-load map on click to avoid rendering issues in hidden divs
        setTimeout(() => {
          if (!mapLoaded) {
            initMobileMap();
            mapLoaded = true;
          } else {
            mobileMap.invalidateSize();
          }
        }, 100);

      } else if (tabName === 'profil') {
        document.getElementById('tab-profil').classList.remove('hidden');
        document.getElementById('nav-profil').className = "flex flex-col items-center gap-1 text-gov-600 transition-colors";
      }
    }

    function initMobileMap() {
      mobileMap = L.map('mobile-map').setView([-7.39675, 109.69724], 11);
      L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19
      }).addTo(mobileMap);

      const projects = @json($allProjects);

      projects.forEach(p => {
        let color = '#ef4444';
        if (p.progress >= 80) {
          color = '#10b981';
        } else if (p.progress >= 40) {
          color = '#f59e0b';
        }

        const marker = L.circleMarker([p.latitude, p.longitude], {
          radius: 8,
          fillColor: color,
          color: '#ffffff',
          weight: 2,
          opacity: 1,
          fillOpacity: 0.95
        }).addTo(mobileMap);

        marker.bindPopup(`
          <div class="font-sans text-xs p-1">
            <div class="font-bold text-slate-900">${p.nama_pekerjaan}</div>
            <div class="text-[9px] text-slate-500 font-semibold mt-1">Progress: ${parseFloat(p.progress).toFixed(1)}%</div>
          </div>
        `);
      });
    }
  </script>
</body>
</html>
