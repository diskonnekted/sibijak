<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Autentikasi SIBIJAK Banjarnegara</title>
  
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
              750: '#115e59',
              800: '#15803d',
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
      --vibe-background: #0f172a;
      --vibe-surface: #1e293b;
      --vibe-text-main: #f8fafc;
      --vibe-text-sub: #94a3b8;
      --vibe-accent-1: #0f3d24;
      --vibe-accent-2: #16a34a;
      --vibe-font-head: 'Outfit', sans-serif;
      --vibe-font-main: 'Plus Jakarta Sans', sans-serif;
      --radius-md: 12px;
      --vibe-transition: all 0.2s ease-in-out;
    }
    h1, h2, h3, h4, .font-heading {
      font-family: var(--vibe-font-head);
    }
  </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-[100dvh] h-[100dvh] flex items-center justify-center p-3 sm:p-6 relative overflow-hidden">
  
  <!-- Hero Background Image Overlay -->
  <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
  <div class="absolute inset-0 bg-gradient-to-b from-gov-950/95 via-slate-900/95 to-slate-900"></div>

  <!-- Main Container -->
  <div class="relative z-10 w-full max-w-4xl space-y-4 sm:space-y-6 my-auto">
    
    <!-- PROMINENT HERO BANNER CARD (GAMBAR HERO PROMINEN SIBIJAK) -->
    <div class="relative rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 shadow-2xl">
      <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-50" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-gov-950/95 via-slate-900/85 to-slate-950/90"></div>
      
      <div class="relative z-10 p-5 sm:p-7 text-center md:text-left">
        <div class="space-y-2">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-gov-100/10 border border-white/10 text-slate-300 text-[10px] font-bold tracking-wider uppercase">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>PEMERINTAH KABUPATEN BANJARNEGARA</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white uppercase font-heading leading-none">
            PORTAL LOG IN SIBIJAK
          </h1>
          <p class="text-xs text-slate-300 font-medium max-w-lg">
            Sistem Informasi Pembinaan Jasa Konstruksi Dinas PUPR. Pilih peran autentikasi Anda di bawah ini untuk mengakses portal layanan.
          </p>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
      <div class="max-w-md mx-auto p-4 bg-red-950/80 border border-red-800 rounded text-red-200 text-xs font-semibold flex items-start gap-3 shadow-lg">
        <i class="ph-bold ph-warning-circle text-xl text-red-400 flex-shrink-0 mt-0.5"></i>
        <div class="space-y-1">
          @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        </div>
      </div>
    @endif

    <!-- ========================================================= -->
    <!-- 1. DESKTOP VIEW (3 Cards Grid — Tampil pada Layar ≥768px) -->
    <!-- ========================================================= -->
    <div class="hidden md:grid grid-cols-3 gap-6">
      
      <!-- Card 1: Admin PUPR -->
      <div class="bg-slate-950/90 border border-slate-800 hover:border-amber-500/60 rounded p-6 shadow-xl flex flex-col justify-between space-y-5 transition-all hover:-translate-y-1 group">
        <div class="space-y-4">
          <div class="w-10 h-10 rounded bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-md">
            <i class="ph-bold ph-shield-checkered text-xl"></i>
          </div>
          <div>
            <span class="px-2 py-0.5 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded text-[9px] font-extrabold uppercase tracking-wider">Dinas PUPR</span>
            <h2 class="text-base font-extrabold text-white mt-1.5 group-hover:text-amber-400 transition-colors font-heading">Admin PUPR</h2>
            <p class="text-xs text-slate-400 leading-relaxed mt-1">Kelola direktori badan usaha, proyek fisik, peta GIS, dan analisa anggaran.</p>
          </div>
        </div>
        
        <div class="space-y-3 pt-3 border-t border-slate-800">
          <div class="text-[10px] text-slate-400">
            <span class="text-slate-500 block">Kredensial Demo:</span>
            <code class="text-amber-300 font-mono">admin@pupr.banjarnegara.go.id</code>
          </div>
          <a href="/admin/login" class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs uppercase tracking-wider rounded shadow-md flex items-center justify-center gap-2 transition-all">
            <span>Masuk Admin</span>
            <i class="ph-bold ph-arrow-right text-sm"></i>
          </a>
        </div>
      </div>

      <!-- Card 2: Kontraktor -->
      <div class="bg-slate-950/90 border border-slate-800 hover:border-blue-500/60 rounded p-6 shadow-xl flex flex-col justify-between space-y-5 transition-all hover:-translate-y-1 group">
        <div class="space-y-4">
          <div class="w-10 h-10 rounded bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400 shadow-md">
            <i class="ph-bold ph-hard-hat text-xl"></i>
          </div>
          <div>
            <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded text-[9px] font-extrabold uppercase tracking-wider">Penyedia Jasa</span>
            <h2 class="text-base font-extrabold text-white mt-1.5 group-hover:text-blue-400 transition-colors font-heading">Kontraktor</h2>
            <p class="text-xs text-slate-400 leading-relaxed mt-1">Pelaporan mandiri progres fisik proyek, kendala lapangan, dan unggah foto.</p>
          </div>
        </div>
        
        <div class="space-y-3 pt-3 border-t border-slate-800">
          <div class="text-[10px] text-slate-400">
            <span class="text-slate-500 block">Kredensial Demo:</span>
            <code class="text-blue-300 font-mono">kontraktor@sikap.id</code>
          </div>
          <a href="/kontraktor/login" class="w-full py-3 px-4 bg-white text-gov-950 hover:bg-slate-100 font-bold text-xs uppercase tracking-wider rounded shadow-md flex items-center justify-center gap-2 transition-all">
            <span>Masuk Kontraktor</span>
            <i class="ph-bold ph-arrow-right text-sm"></i>
          </a>
        </div>
      </div>

      <!-- Card 3: Pengawas Lapangan -->
      <div class="bg-slate-950/90 border border-slate-800 hover:border-emerald-500/60 rounded p-6 shadow-xl flex flex-col justify-between space-y-5 transition-all hover:-translate-y-1 group">
        <div class="space-y-4">
          <div class="w-10 h-10 rounded bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-md">
            <i class="ph-bold ph-clipboard-text text-xl"></i>
          </div>
          <div>
            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded text-[9px] font-extrabold uppercase tracking-wider">Verifikator</span>
            <h2 class="text-base font-extrabold text-white mt-1.5 group-hover:text-emerald-400 transition-colors font-heading">Pengawas Lapangan</h2>
            <p class="text-xs text-slate-400 leading-relaxed mt-1">Verifikasi kecocokan progres fisik, inspeksi foto lapangan, dan persetujuan.</p>
          </div>
        </div>
        
        <div class="space-y-3 pt-3 border-t border-slate-800">
          <div class="text-[10px] text-slate-400">
            <span class="text-slate-500 block">Kredensial Demo:</span>
            <code class="text-emerald-300 font-mono">pemeriksa@sikap.id</code>
          </div>
          <a href="/pengawas/login" class="w-full py-3 px-4 bg-gov-600 hover:bg-gov-500 text-white font-bold text-xs uppercase tracking-wider rounded shadow-md flex items-center justify-center gap-2 transition-all">
            <span>Masuk Pengawas</span>
            <i class="ph-bold ph-arrow-right text-sm"></i>
          </a>
        </div>
      </div>

    </div>

    <!-- ========================================================= -->
    <!-- 2. MOBILE VIEW (Interactive Role Selector Form <768px)    -->
    <!-- ========================================================= -->
    <div class="block md:hidden max-w-md mx-auto">
      <div class="bg-slate-950 border border-slate-800 rounded p-4 sm:p-6 shadow-2xl space-y-4">
        
        <!-- Segmented Role Selector Tabs -->
        <div class="space-y-1.5">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pilih Peran Autentikasi</label>
          <div class="grid grid-cols-3 gap-1 bg-slate-900 p-1 rounded border border-slate-800 text-xs font-bold">
            <button type="button" onclick="setSingleMobileRole('kontraktor')" id="m-role-kontraktor" class="py-2.5 rounded text-center transition-all bg-white text-slate-950 shadow-sm font-extrabold uppercase">Kontraktor</button>
            <button type="button" onclick="setSingleMobileRole('pengawas')" id="m-role-pengawas" class="py-2.5 rounded text-center transition-all text-slate-400 hover:text-white uppercase">Pengawas</button>
            <button type="button" onclick="setSingleMobileRole('admin')" id="m-role-admin" class="py-2.5 rounded text-center transition-all text-slate-400 hover:text-white uppercase">Admin</button>
          </div>
        </div>

        <!-- Role Badge Header Inside Card -->
        <div id="singleRoleBadge" class="p-3.5 rounded border bg-blue-500/10 border-blue-500/30 flex items-center gap-3">
          <div id="singleRoleIcon" class="w-9 h-9 rounded flex items-center justify-center text-lg flex-shrink-0 bg-blue-500/20 text-blue-400">
            <i class="ph-bold ph-hard-hat"></i>
          </div>
          <div>
            <h3 id="singleRoleTitle" class="text-xs font-extrabold text-white font-heading uppercase">LOGIN KONTRAKTOR</h3>
            <p id="singleRoleDesc" class="text-[10px] text-slate-400 leading-tight">Pelaporan Mandiri Progres Fisik Lapangan</p>
          </div>
        </div>

        <!-- Single Form -->
        <form id="singleMobileForm" action="/kontraktor/login" method="POST" autocomplete="off" class="space-y-4 text-xs">
          @csrf

          <div>
            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email Kredensial</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                <i class="ph-bold ph-envelope text-base"></i>
              </span>
              <input type="email" id="singleEmail" name="email" value="kontraktor@sikap.id" required autocomplete="off" class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-800 rounded text-white focus:outline-none focus:border-gov-600 transition-all font-medium">
            </div>
          </div>

          <div>
            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                <i class="ph-bold ph-lock-key text-base"></i>
              </span>
              <input type="password" name="password" value="password" required autocomplete="new-password" class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-800 rounded text-white focus:outline-none focus:border-gov-600 transition-all font-medium">
            </div>
          </div>

          <div class="flex items-center justify-between pt-1">
            <label class="flex items-center text-slate-400 cursor-pointer text-[11px]">
              <input type="checkbox" name="remember" checked class="w-3.5 h-3.5 rounded border-slate-800 text-gov-600 bg-slate-900 focus:ring-gov-600">
              <span class="ml-1.5 font-medium">Ingat perangkat ini</span>
            </label>
          </div>

          <button type="submit" id="singleSubmitBtn" class="w-full py-3.5 px-4 font-bold text-xs uppercase tracking-wider rounded shadow-md transition-all flex items-center justify-center gap-2 bg-white text-slate-950 active:scale-[0.98]">
            <span>Masuk Sistem</span>
            <i class="ph-bold ph-sign-in text-base"></i>
          </button>
        </form>

      </div>
    </div>

    <!-- Navigation Back link & Clean Footer -->
    <div class="text-center pt-2 space-y-2">
      <a href="/" class="inline-flex items-center gap-2 px-3 py-1 bg-slate-900 border border-slate-800 rounded text-xs font-semibold text-slate-400 hover:text-white transition-colors">
        <i class="ph-bold ph-house"></i> Kembali ke Beranda Portal Utama
      </a>
      <p class="text-[11px] text-slate-500 font-medium">© {{ date('Y') }} Dinas Pekerjaan Umum & Penataan Ruang Kabupaten Banjarnegara.</p>
    </div>

  </div>

  <!-- JavaScript for Single Mobile Form Role Switcher -->
  <script>
    const mobileRolesMap = {
      kontraktor: {
        action: '/kontraktor/login',
        title: 'LOGIN KONTRAKTOR',
        desc: 'Pelaporan Mandiri Progres Fisik Lapangan',
        email: 'kontraktor@sikap.id',
        badgeBg: 'bg-blue-500/10 border-blue-500/30',
        iconClass: 'ph-hard-hat text-blue-400 bg-blue-500/20',
        btnClass: 'bg-white text-slate-950',
        activeTab: 'bg-white text-slate-950 shadow-sm'
      },
      pengawas: {
        action: '/pengawas/login',
        title: 'LOGIN PENGAWAS LAPANGAN',
        desc: 'Inspeksi & Approval Laporan Fisik Kontraktor',
        email: 'pemeriksa@sikap.id',
        badgeBg: 'bg-emerald-500/10 border-emerald-500/30',
        iconClass: 'ph-clipboard-text text-emerald-400 bg-emerald-500/20',
        btnClass: 'bg-gov-600 text-white',
        activeTab: 'bg-gov-600 text-white shadow-sm'
      },
      admin: {
        action: '/admin/login',
        title: 'LOGIN ADMIN PUPR',
        desc: 'Akses Pengelolaan Data Dinas & Spasial GIS',
        email: 'admin@pupr.banjarnegara.go.id',
        badgeBg: 'bg-amber-500/10 border-amber-500/30',
        iconClass: 'ph-shield-checkered text-amber-400 bg-amber-500/20',
        btnClass: 'bg-amber-500 text-slate-950',
        activeTab: 'bg-amber-500 text-slate-950 shadow-sm'
      }
    };

    function setSingleMobileRole(role) {
      const data = mobileRolesMap[role] || mobileRolesMap['kontraktor'];
      
      document.getElementById('singleMobileForm').action = data.action;
      document.getElementById('singleRoleTitle').innerText = data.title;
      document.getElementById('singleRoleDesc').innerText = data.desc;
      document.getElementById('singleEmail').value = data.email;
      
      const badge = document.getElementById('singleRoleBadge');
      badge.className = `p-3.5 rounded border flex items-center gap-3 ${data.badgeBg}`;
      
      const icon = document.getElementById('singleRoleIcon');
      icon.className = `w-9 h-9 rounded flex items-center justify-center text-lg flex-shrink-0 ${data.iconClass.split(' ').slice(1).join(' ')}`;
      icon.innerHTML = `<i class="ph-bold ${data.iconClass.split(' ')[0]}"></i>`;
      
      const btn = document.getElementById('singleSubmitBtn');
      btn.className = `w-full py-3.5 px-4 font-bold text-xs uppercase tracking-wider rounded shadow-md transition-all flex items-center justify-center gap-2 active:scale-[0.98] ${data.btnClass}`;

      // Reset Tab Styles
      ['kontraktor', 'pengawas', 'admin'].forEach(r => {
        const tab = document.getElementById(`m-role-${r}`);
        if (r === role) {
          tab.className = `py-2.5 rounded text-center transition-all uppercase font-extrabold ${data.activeTab}`;
        } else {
          tab.className = 'py-2.5 rounded text-center transition-all text-slate-400 hover:text-white uppercase';
        }
      });
    }

    // Auto-check URL parameter e.g. /login?role=pengawas
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const roleParam = urlParams.get('role');
      if (roleParam && mobileRolesMap[roleParam]) {
        setSingleMobileRole(roleParam);
      }
    });
  </script>

</body>
</html>
