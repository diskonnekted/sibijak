<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Kontraktor — SIBIJAK Banjarnegara</title>
  
  <!-- Font and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Tailwind CSS -->
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
            contractor: {
              50: '#eff6ff',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
              900: '#1e3a8a',
            }
          }
        }
      }
    }
  </script>
  <style>
    h1, h2, h3, h4, .font-heading {
      font-family: 'Outfit', sans-serif;
    }
  </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen flex items-center justify-center relative overflow-x-hidden overflow-y-auto py-8">
  
  <!-- Background Image Overlay -->
  <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-25" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
  <div class="absolute inset-0 bg-gradient-to-b from-gov-950/95 via-slate-900/95 to-slate-900"></div>

  <!-- Main Card -->
  <div class="relative z-10 w-full max-w-md px-6 py-8">
    
    <!-- Top Back to Portal Button -->
    <div class="mb-4">
      <a href="/login" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
        <i class="ph-bold ph-arrow-left"></i> Hub Pemilih Login Peran
      </a>
    </div>

    <div class="bg-slate-950/90 border border-slate-800 rounded p-8 shadow-2xl space-y-6 relative overflow-hidden">
      
      <!-- Role Accent Line -->
      <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-sky-400"></div>

      <!-- Badge Header -->
      <div class="flex flex-col items-center text-center space-y-3 pt-2">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-gov-100/10 border border-white/10 text-slate-300 text-[10px] font-bold tracking-wider uppercase">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
          <span>PEMERINTAH KABUPATEN BANJARNEGARA</span>
        </div>

        <div class="w-12 h-12 rounded bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400 shadow-md">
          <i class="ph-bold ph-hard-hat text-2xl"></i>
        </div>
        <div>
          <h1 class="text-xl font-extrabold text-white mt-1 uppercase font-heading">KONTRAKTOR KONSTRUKSI</h1>
          <p class="text-xs text-slate-400 font-medium">Pelaporan Mandiri Progres & Foto Lapangan</p>
        </div>
      </div>

      <!-- Error alert -->
      @if($errors->any())
        <div class="p-4 bg-red-950/60 border border-red-800/80 rounded-2xl text-red-300 text-xs font-semibold flex items-start gap-3">
          <i class="ph-bold ph-warning-circle text-xl text-red-400 flex-shrink-0 mt-0.5"></i>
          <div class="space-y-1">
            @foreach ($errors->all() as $error)
              <p>{{ $error }}</p>
            @endforeach
          </div>
        </div>
      @endif

      <!-- Kontraktor Login Form -->
      <form action="/kontraktor/login" method="POST" autocomplete="off" class="space-y-5">
        @csrf
        
        <div>
          <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-2">Email Perusahaan / Rekanan</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
              <i class="ph-bold ph-envelope text-lg"></i>
            </span>
            <input type="email" id="emailInput" name="email" value="{{ old('email', 'kontraktor@sikap.id') }}" required autocomplete="off" placeholder="kontraktor@sikap.id" class="w-full pl-11 pr-4 py-3.5 text-sm bg-slate-950/90 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all">
          </div>
        </div>

        <div>
          <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-2">Kata Sandi</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
              <i class="ph-bold ph-lock-key text-lg"></i>
            </span>
            <input type="password" name="password" value="password" required autocomplete="new-password" placeholder="••••••••" class="w-full pl-11 pr-4 py-3.5 text-sm bg-slate-950/90 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all">
          </div>
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center text-xs text-slate-400 cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-800 text-blue-500 bg-slate-950 focus:ring-blue-500">
            <span class="ml-2 font-medium">Ingat perangkat ini</span>
          </label>
          <a href="/daftar" class="text-[11px] text-blue-400 hover:underline font-semibold">Belum Terdaftar?</a>
        </div>

        <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2 group">
          <span>Masuk Aplikasi Kontraktor</span>
          <i class="ph-bold ph-sign-in text-lg group-hover:translate-x-1 transition-transform"></i>
        </button>
      </form>

      <!-- Footer Info -->
      <div class="pt-2 border-t border-slate-800/80 text-center">
        <p class="text-[11px] text-slate-500">Aplikasi Web Mobile Pelaporan Progress Proyek Fisik</p>
        <a href="{{ route('password.request') }}" class="mt-1 inline-block text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">Lupa kata sandi?</a>
      </div>

    </div>
  </div>

</body>
</html>
