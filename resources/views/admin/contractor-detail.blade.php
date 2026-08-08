<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Kontraktor: {{ $contractor->name }} - Admin SIKAP</title>
  
  <!-- Font and Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
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
    body {
      font-feature-settings: "cv02", "cv03", "cv04", "cv11";
    }
  </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased">

  <!-- ADMIN HEADER -->
  <header class="bg-gov-900 text-white shadow-md sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="w-9 h-9 rounded bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-all">
          <i class="ph-bold ph-arrow-left text-lg"></i>
        </a>
        <div>
          <span class="block text-sm font-extrabold tracking-tight uppercase leading-none">Profil Rekanan Jasa Konstruksi</span>
          <span class="text-[9px] font-semibold text-slate-300 tracking-wider uppercase">Dinas PUPR Banjarnegara</span>
        </div>
      </div>
      <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold uppercase tracking-wider text-slate-300 hover:text-white flex items-center gap-1">
        <i class="ph ph-layout"></i> Kembali ke Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-6 py-8 space-y-6">
    
    <!-- Profile & Metrics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      
      <!-- Identity card -->
      <div class="lg:col-span-8 bg-white p-6 rounded border border-slate-200 shadow-sm space-y-6">
        <div class="flex justify-between items-start">
          <div class="space-y-1.5">
            <span class="inline-block px-2.5 py-0.5 rounded bg-slate-100 border border-slate-200 text-[10px] font-bold text-slate-600 uppercase tracking-wide">
              Kualifikasi {{ $contractor->kualifikasi }}
            </span>
            <h2 class="text-xl font-extrabold text-slate-900">{{ $contractor->name }}</h2>
            <span class="block font-mono text-xs text-slate-500">NIB: {{ $contractor->nib }}</span>
          </div>
          
          <div class="flex items-center gap-1 text-sm font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded border border-amber-100">
            <i class="ph-fill ph-star"></i> {{ number_format($contractor->rating, 1) }}
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6 pt-4 border-t border-slate-100 text-xs">
          <div>
            <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Klasifikasi Utama</span>
            <span class="text-sm font-bold text-slate-800">{{ $contractor->bidang }}</span>
          </div>
          <div>
            <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Penanggung Jawab (Direktur)</span>
            <span class="text-sm font-bold text-slate-800">{{ $contractor->pj }}</span>
          </div>
          <div>
            <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Kontak Perusahaan</span>
            <span class="text-sm font-bold text-slate-800">{{ $contractor->email ?? '-' }} / {{ $contractor->telepon ?? '-' }}</span>
          </div>
          <div>
            <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Alamat Kantor</span>
            <span class="text-sm font-bold text-slate-800 leading-relaxed">{{ $contractor->alamat }}</span>
          </div>
        </div>
      </div>

      <!-- Financial / Projects summary -->
      <div class="lg:col-span-4 space-y-6">
        <!-- Metric 1: Total Contract Values -->
        <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Nilai Proyek</span>
            <span class="text-lg font-extrabold text-slate-900 leading-none">Rp {{ number_format($totalContractValue, 0, ',', '.') }}</span>
          </div>
          <div class="w-12 h-12 rounded bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
            <i class="ph-bold ph-currency-circle-dollar text-2xl"></i>
          </div>
        </div>

        <!-- Metric 2: Project Progress average -->
        <div class="bg-white p-6 rounded border border-slate-200 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Rata-rata Progress Fisik</span>
            <span class="text-2xl font-extrabold text-gov-600 leading-none">{{ number_format($averageProgress, 1) }}%</span>
          </div>
          <div class="w-12 h-12 rounded bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
            <i class="ph-bold ph-chart-line text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Projects Table Card -->
    <div class="bg-white rounded border border-slate-200 overflow-hidden shadow-sm">
      <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Paket Pekerjaan Terlaksana</h3>
        <span class="text-xs font-bold bg-gov-100 text-gov-800 px-2 py-0.5 rounded">{{ $contractor->projects->count() }} Paket</span>
      </div>

      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <th class="pb-3 font-semibold">Nama Pekerjaan</th>
                <th class="pb-3 font-semibold">Nilai Kontrak</th>
                <th class="pb-3 font-semibold">T.A.</th>
                <th class="pb-3 font-semibold">Status</th>
                <th class="pb-3 font-semibold">Progres Fisik</th>
                <th class="pb-3 font-semibold">Koordinat Lokasi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
              @forelse($contractor->projects as $p)
                <tr>
                  <td class="py-4 font-bold text-slate-900">{{ $p->nama_pekerjaan }}</td>
                  <td class="py-4 font-semibold text-slate-800">Rp {{ number_format($p->nilai_kontrak, 0, ',', '.') }}</td>
                  <td class="py-4 font-mono text-xs">{{ $p->tahun_anggaran }}</td>
                  <td class="py-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{
                      $p->status === 'Selesai' ? 'bg-gov-100 text-gov-800' : 'bg-amber-100 text-amber-800'
                    }}">
                      {{ $p->status }}
                    </span>
                  </td>
                  <td class="py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-24 bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-gov-600 h-2 rounded-full" style="width: {{ $p->progress }}%"></div>
                      </div>
                      <span class="font-bold text-xs">{{ number_format($p->progress, 0) }}%</span>
                    </div>
                  </td>
                  <td class="py-4 font-mono text-xs text-slate-500">{{ $p->latitude }}, {{ $p->longitude }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada riwayat pekerjaan proyek terdaftar.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
