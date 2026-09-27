<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'SIKAP Admin - PUPR Banjarnegara')</title>
  
  <!-- Font and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
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
            mono: ['"JetBrains Mono"', 'monospace'],
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
    :root {
      --vibe-background: #f8fafc;
      --vibe-surface: #ffffff;
      --vibe-text-main: #0f172a;
      --vibe-text-sub: #64748b;
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

  @yield('styles')
</head>
<body class="bg-slate-950 text-slate-900 font-sans antialiased flex min-h-screen">

  @php
    $allUsers = \App\Models\User::all();
    $currentUser = auth()->user();
    $currentRole = $currentUser ? $currentUser->role : 'admin_pupr';
  @endphp

  <!-- SIDEBAR MENU (Hanya Tampil pada Layar md:flex >=768px) -->
  <aside class="hidden md:flex w-64 bg-slate-900 text-slate-300 flex-shrink-0 flex-col justify-between border-r border-slate-800">
    <div class="space-y-8">
      <!-- Sidebar Header (Logo) -->
      <div class="h-16 px-6 flex items-center gap-3 border-b border-slate-800 bg-slate-950">
        <div class="w-8 h-8 rounded bg-gov-600 flex items-center justify-center text-white">
          <i class="ph-bold ph-shield-checkered text-lg"></i>
        </div>
        <div>
          <span class="block text-xs font-extrabold tracking-widest text-white uppercase leading-none">SIKAP PANEL</span>
          <span class="text-[9px] font-semibold text-slate-500 tracking-wider uppercase">PUPR Banjarnegara</span>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="px-4 space-y-1.5">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-sm font-semibold tracking-wide {{ Route::is('admin.dashboard') ? 'bg-gov-900 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }} transition-all">
          <i class="ph-bold ph-layout text-lg"></i>
          <span>Dashboard Utama</span>
        </a>
        <a href="{{ route('admin.map') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-sm font-semibold tracking-wide {{ Route::is('admin.map') ? 'bg-gov-900 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }} transition-all">
          <i class="ph-bold ph-map-trifold text-lg"></i>
          <span>Peta Monitoring</span>
        </a>
        
        @if($currentRole !== 'kontraktor')
          <a href="{{ route('admin.analysis') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-sm font-semibold tracking-wide {{ Route::is('admin.analysis') ? 'bg-gov-900 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }} transition-all">
            <i class="ph-bold ph-chart-bar text-lg"></i>
            <span>Analisis & Rekomendasi</span>
          </a>
        @endif

        @if($currentRole === 'admin_pupr')
          <a href="{{ route('admin.cms') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-sm font-semibold tracking-wide {{ Request::is('admin/cms*') ? 'bg-gov-900 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }} transition-all">
            <i class="ph-bold ph-newspaper text-lg"></i>
            <span>Kelola Konten</span>
          </a>
          <a href="{{ route('admin.logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-sm font-semibold tracking-wide {{ Route::is('admin.logs') ? 'bg-gov-900 text-white shadow-sm' : 'hover:bg-slate-800 hover:text-white' }} transition-all">
            <i class="ph-bold ph-shield-check text-lg"></i>
            <span>Log Aktivitas User</span>
          </a>
        @endif
      </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between gap-3">
      <div class="flex items-center gap-3 min-w-0">
        <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white border border-slate-700 flex-shrink-0">
          <i class="ph ph-user"></i>
        </div>
        <div class="min-w-0">
          <span class="block text-xs font-bold text-white leading-none truncate">{{ $currentUser ? $currentUser->name : 'Admin PUPR' }}</span>
          <span class="text-[9px] text-slate-500 font-semibold uppercase tracking-wide block mt-1">
            {{ $currentUser ? str_replace('_', ' ', $currentUser->role) : 'ADMIN PUPR' }}
          </span>
        </div>
      </div>
      
      <!-- Logout Button -->
      <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
        @csrf
        <button type="submit" class="p-1.5 text-slate-500 hover:text-white rounded hover:bg-slate-850 transition-colors" title="Log Out / Keluar">
          <i class="ph-bold ph-sign-out text-base"></i>
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN AREA -->
  <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden bg-slate-950 md:bg-slate-50">
    <!-- TOP HEADER DESKTOP (Hanya Tampil pada Layar md:flex >=768px) -->
    <header class="hidden md:flex h-16 bg-white border-b border-slate-200 px-6 items-center justify-between z-20">
      <div class="flex items-center gap-4">
        <h1 class="text-base font-extrabold text-slate-900">@yield('page_title', 'SIKAP Admin')</h1>
      </div>
      
      <div class="flex items-center gap-4">
        <span class="text-xs text-slate-500 font-semibold hidden md:inline mr-2">
          {{ date('d F Y') }}
        </span>
        
        <!-- Logout Form -->
        <form action="{{ route('logout') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold tracking-wide uppercase text-white bg-red-600 hover:bg-red-700 rounded transition-all active:scale-[0.98]">
            <i class="ph-bold ph-sign-out text-base"></i> Keluar
          </button>
        </form>
      </div>
    </header>

    <!-- CONTENT WRAPPER -->
    <div class="p-0 md:p-8 flex-1 overflow-y-auto">
      @yield('content')
    </div>
  </div>

  <!-- FLOATING BACK TO TOP BUTTON -->
  <button id="admBackToTopBtn" type="button" onclick="scrollAdminToTop()" class="fixed bottom-20 md:bottom-8 right-5 z-40 w-11 h-11 rounded-full bg-gov-600 hover:bg-gov-500 text-white border border-emerald-400/40 shadow-2xl flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none active:scale-95 group" title="Kembali ke atas">
    <i class="ph-bold ph-arrow-up text-lg group-hover:-translate-y-0.5 transition-transform"></i>
  </button>

  <script>
    const admBackToTopBtn = document.getElementById('admBackToTopBtn');
    if (admBackToTopBtn) {
      window.addEventListener('scroll', function() {
        if (window.scrollY > 250) {
          admBackToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
          admBackToTopBtn.classList.add('opacity-100');
        } else {
          admBackToTopBtn.classList.add('opacity-0', 'pointer-events-none');
          admBackToTopBtn.classList.remove('opacity-100');
        }
      });
    }

    function scrollAdminToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }
  </script>

  @yield('scripts')
</body>
</html>
