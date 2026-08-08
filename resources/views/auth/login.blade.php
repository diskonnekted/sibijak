<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login SIKAP - PUPR Banjarnegara</title>
  
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
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen flex items-center justify-center relative overflow-hidden">
  
  <!-- Background blur shapes -->
  <div class="absolute w-[500px] h-[500px] rounded-full bg-gov-900/20 blur-[120px] -top-40 -left-40 z-0"></div>
  <div class="absolute w-[500px] h-[500px] rounded-full bg-emerald-900/10 blur-[120px] -bottom-40 -right-40 z-0"></div>

  <!-- Main Login Card -->
  <div class="relative z-10 w-full max-w-md px-6 py-12">
    <div class="bg-slate-950/80 backdrop-blur-md border border-slate-800 rounded-2xl p-8 shadow-2xl space-y-8">
      
      <!-- Logo and Titles -->
      <div class="text-center space-y-3">
        <div class="inline-flex w-12 h-12 rounded-xl bg-gov-600 items-center justify-center text-white shadow-lg shadow-gov-600/20">
          <i class="ph-bold ph-shield-checkered text-2xl"></i>
        </div>
        <div class="space-y-1">
          <h1 class="text-lg font-extrabold tracking-tight text-white uppercase">SIKAP BANJARNEGARA</h1>
          <p class="text-xs text-slate-400 font-medium">Sistem Informasi Pembina Jasa Konstruksi & Fisik</p>
        </div>
      </div>

      <!-- Error alert -->
      @if($errors->any())
        <div class="p-4 bg-red-950/50 border border-red-800/80 rounded-xl text-red-400 text-xs font-semibold flex items-start gap-3">
          <i class="ph-bold ph-warning-circle text-lg flex-shrink-0 mt-0.5"></i>
          <div>
            @foreach ($errors->all() as $error)
              <p>{{ $error }}</p>
            @endforeach
          </div>
        </div>
      @endif

      <!-- Login Form -->
      <form action="/login" method="POST" class="space-y-5">
        @csrf
        
        <div>
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Alamat Email</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
              <i class="ph-bold ph-envelope text-base"></i>
            </span>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@instansi.go.id" class="w-full pl-10 pr-4 py-3 text-sm bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-gov-600 focus:border-gov-600 transition-all">
          </div>
        </div>

        <div>
          <div class="flex justify-between items-center mb-1.5">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kata Sandi</label>
          </div>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
              <i class="ph-bold ph-lock-key text-base"></i>
            </span>
            <input type="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-3 text-sm bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-gov-600 focus:border-gov-600 transition-all">
          </div>
        </div>

        <div class="flex items-center">
          <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-slate-800 text-gov-600 bg-slate-900 focus:ring-gov-600 focus:ring-offset-slate-950">
          <label for="remember" class="ml-2 text-xs font-semibold text-slate-400 cursor-pointer">Ingat saya di perangkat ini</label>
        </div>

        <button type="submit" class="w-full py-3 bg-gov-600 hover:bg-gov-800 active:scale-[0.98] text-sm font-bold text-white rounded-xl shadow-lg shadow-gov-600/10 transition-all">
          Masuk ke Panel Admin
        </button>
      </form>

      <!-- Testing accounts help note -->
      <div class="pt-6 border-t border-slate-900 space-y-3">
        <span class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider text-center">Akun Demo Uji Coba (Password: password)</span>
        <div class="grid grid-cols-1 gap-2 text-[10px] text-slate-400 font-medium">
          <div class="bg-slate-900 p-2.5 rounded-lg border border-slate-850 flex justify-between">
            <span>Admin PUPR:</span>
            <strong class="text-slate-200">admin@pupr.banjarnegara.go.id</strong>
          </div>
          <div class="bg-slate-900 p-2.5 rounded-lg border border-slate-850 flex justify-between">
            <span>Kontraktor Prima Karya:</span>
            <strong class="text-slate-200">kontraktor@sikap.id</strong>
          </div>
          <div class="bg-slate-900 p-2.5 rounded-lg border border-slate-850 flex justify-between">
            <span>Pemeriksa Lapangan:</span>
            <strong class="text-slate-200">pemeriksa@sikap.id</strong>
          </div>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
