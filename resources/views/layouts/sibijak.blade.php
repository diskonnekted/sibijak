<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'SIBIJAK - Pembinaan Jasa Konstruksi Banjarnegara')</title>
  
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
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-gov-900 selection:text-white">

  <!-- HEADER / NAVBAR -->
  <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
      <!-- Logo -->
      <a href="{{ route('portal') }}" class="flex items-center gap-3 active:scale-[0.98] transition-transform">
        <div class="w-10 h-10 rounded bg-gov-900 flex items-center justify-center text-white shadow-md shadow-gov-900/10">
          <i class="ph-bold ph-shield-checkered text-xl"></i>
        </div>
        <div>
          <span class="block text-sm font-extrabold tracking-tight text-gov-950 uppercase leading-none">SIBIJAK</span>
          <span class="text-[9px] font-bold text-slate-500 tracking-wider uppercase">Dinas PUPR Banjarnegara</span>
        </div>
      </a>

      <!-- Desktop Nav -->
      <nav class="hidden md:flex items-center gap-6">
        <a href="{{ route('portal') }}" class="text-xs font-bold tracking-wider uppercase {{ Route::is('portal') ? 'text-gov-900 border-b-2 border-gov-900' : 'text-slate-600 hover:text-gov-900' }} py-5 transition-all">Beranda</a>
        <a href="{{ route('badanusaha') }}" class="text-xs font-bold tracking-wider uppercase {{ Route::is('badanusaha') ? 'text-gov-900 border-b-2 border-gov-900' : 'text-slate-600 hover:text-gov-900' }} py-5 transition-all">Badan Usaha</a>
        <a href="{{ route('pelatihan') }}" class="text-xs font-bold tracking-wider uppercase {{ Route::is('pelatihan') ? 'text-gov-900 border-b-2 border-gov-900' : 'text-slate-600 hover:text-gov-900' }} py-5 transition-all">Pelatihan</a>
        <a href="{{ route('regulasi') }}" class="text-xs font-bold tracking-wider uppercase {{ Route::is('regulasi') ? 'text-gov-900 border-b-2 border-gov-900' : 'text-slate-600 hover:text-gov-900' }} py-5 transition-all">Peraturan</a>
        <a href="{{ route('berita') }}" class="text-xs font-bold tracking-wider uppercase {{ Route::is('berita') ? 'text-gov-900 border-b-2 border-gov-900' : 'text-slate-600 hover:text-gov-900' }} py-5 transition-all">Berita</a>
      </nav>

      <!-- Desktop CTAs -->
      <div class="hidden md:flex items-center gap-4">
        <a href="{{ route('daftar') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold tracking-wide uppercase text-slate-700 hover:text-slate-900 border border-slate-300 hover:border-slate-400 bg-transparent rounded transition-all active:scale-[0.98]">
          Pendaftaran
        </a>
        @auth
          <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold tracking-wide uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98]">
            Dasbor Admin
          </a>
        @else
          <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold tracking-wide uppercase text-white bg-gov-900 hover:bg-gov-950 rounded shadow active:scale-[0.98]">
            Login
          </a>
        @endauth
      </div>
    </div>
  </header>

  <!-- CONTENT VIEW -->
  <main class="min-h-[80dvh]">
    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer class="bg-slate-950 text-slate-400 py-16 border-t border-slate-900">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-12">
      <!-- Info Dinas -->
      <div class="md:col-span-6 space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded bg-white/10 flex items-center justify-center text-white">
            <i class="ph-bold ph-shield-checkered text-lg"></i>
          </div>
          <span class="text-sm font-extrabold tracking-wider text-white uppercase">SIBIJAK Dinas PUPR Banjarnegara</span>
        </div>
        <p class="text-xs leading-relaxed max-w-[45ch]">
          Sistem Informasi Pembinaan Jasa Konstruksi (SIBIJAK) dikelola oleh Bidang Bina Konstruksi dan Bina Manfaat Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Banjarnegara.
        </p>
        <p class="text-xs text-slate-500">
          Jl. Selamanik No. 12, Banjarnegara, Jawa Tengah<br>
          Email: dpupr@banjarnegarakab.go.id | Telp: (0286) 591234
        </p>
      </div>

      <!-- Links menu -->
      <div class="md:col-span-3 space-y-4">
        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Navigasi Utama</h4>
        <ul class="space-y-2 text-xs">
          <li><a href="{{ route('portal') }}" class="hover:text-white transition-colors">Beranda</a></li>
          <li><a href="{{ route('badanusaha') }}" class="hover:text-white transition-colors">Badan Usaha Rekanan</a></li>
          <li><a href="{{ route('pelatihan') }}" class="hover:text-white transition-colors">Pelatihan Tenaga Kerja</a></li>
          <li><a href="{{ route('regulasi') }}" class="hover:text-white transition-colors">Peraturan & Perundang-undangan</a></li>
        </ul>
      </div>

      <!-- Links regulasi -->
      <div class="md:col-span-3 space-y-4">
        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Akses Cepat</h4>
        <ul class="space-y-2 text-xs">
          <li><a href="{{ route('daftar') }}" class="hover:text-white transition-colors">Registrasi Online</a></li>
          <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Masuk Sistem Log</a></li>
        </ul>
      </div>
    </div>

    <!-- Copyright -->
    <div class="max-w-7xl mx-auto px-6 mt-12 pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
      <span>© 2026 Pemerintah Kabupaten Banjarnegara. Hak Cipta Dilindungi.</span>
      <span>Dikembangkan sesuai Pedoman e-Gov Kementerian Kominfo.</span>
    </div>
  <!-- PUBLIC MOBILE STICKY BOTTOM NAVIGATION BAR (Tampil pada Layar <768px) -->
  <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-slate-900/95 backdrop-blur-md border-t border-slate-800 px-4 py-2 shadow-2xl">
    <div class="flex justify-around items-center max-w-md mx-auto">
      <a href="{{ route('portal') }}" class="flex flex-col items-center gap-1 {{ Route::is('portal') ? 'text-gov-400 font-bold' : 'text-slate-400 hover:text-white' }} transition-all">
        <i class="ph-bold ph-house text-xl"></i>
        <span class="text-[9px] uppercase tracking-wider">Beranda</span>
      </a>
      <a href="{{ route('badanusaha') }}" class="flex flex-col items-center gap-1 {{ Route::is('badanusaha') ? 'text-gov-400 font-bold' : 'text-slate-400 hover:text-white' }} transition-all">
        <i class="ph-bold ph-buildings text-xl"></i>
        <span class="text-[9px] uppercase tracking-wider">Kontraktor</span>
      </a>
      <a href="{{ route('regulasi') }}" class="flex flex-col items-center gap-1 {{ Route::is('regulasi') ? 'text-gov-400 font-bold' : 'text-slate-400 hover:text-white' }} transition-all">
        <i class="ph-bold ph-scales text-xl"></i>
        <span class="text-[9px] uppercase tracking-wider">Peraturan</span>
      </a>
      <a href="{{ route('berita') }}" class="flex flex-col items-center gap-1 {{ Route::is('berita') ? 'text-gov-400 font-bold' : 'text-slate-400 hover:text-white' }} transition-all">
        <i class="ph-bold ph-newspaper text-xl"></i>
        <span class="text-[9px] uppercase tracking-wider">Berita</span>
      </a>
      @auth
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 text-emerald-400 font-bold transition-all">
          <i class="ph-bold ph-shield-check text-xl"></i>
          <span class="text-[9px] uppercase tracking-wider">Dasbor</span>
        </a>
      @else
        <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 {{ Route::is('login*') ? 'text-gov-400 font-bold' : 'text-slate-400 hover:text-white' }} transition-all">
          <i class="ph-bold ph-user-circle text-xl"></i>
          <span class="text-[9px] uppercase tracking-wider">Login</span>
        </a>
      @endauth
    </div>
  </nav>

  <!-- FLOATING BACK TO TOP BUTTON -->
  <button id="backToTopBtn" type="button" onclick="scrollToTop()" class="fixed bottom-20 md:bottom-8 right-5 z-40 w-11 h-11 rounded-full bg-gov-600 hover:bg-gov-500 text-white border border-emerald-400/40 shadow-2xl flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none active:scale-95 group" title="Kembali ke atas">
    <i class="ph-bold ph-arrow-up text-lg group-hover:-translate-y-0.5 transition-transform"></i>
  </button>

  <script>
    const backToTopBtn = document.getElementById('backToTopBtn');
    if (backToTopBtn) {
      window.addEventListener('scroll', function() {
        if (window.scrollY > 250) {
          backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
          backToTopBtn.classList.add('opacity-100');
        } else {
          backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
          backToTopBtn.classList.remove('opacity-100');
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

  @yield('scripts')
</body>
</html>
