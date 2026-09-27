@extends('layouts.admin')

@section('title', 'Log Aktivitas User & Audit Trail - SIKAP Admin')
@section('page_title', 'Audit Trail Log Aktivitas Sistem')

@section('content')
  @php
    $currentRole = auth()->user() ? auth()->user()->role : 'admin_pupr';
  @endphp

  <!-- MODE DESKTOP VIEW (>=768px) -->
  <div class="hidden md:block space-y-6">
    
    <!-- PAGE HEADER CARD -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gov-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-gov-600/30">
          <i class="ph-bold ph-shield-check"></i>
        </div>
        <div>
          <h1 class="text-lg font-extrabold font-heading tracking-wide uppercase">Pusat Log Aktivitas & Audit Trail User</h1>
          <p class="text-xs text-slate-300 mt-0.5">Memantau riwayat login/logout, pengajuan progres, verifikasi audit pengawas, dan IP Address (Cloudflare & Reverse Proxy aware).</p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-xs font-mono font-bold px-3.5 py-1.5 bg-emerald-950 text-emerald-300 border border-emerald-800 rounded-full flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Cloudflare Real-IP Active
        </span>
      </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
      <div class="flex justify-between items-center border-b border-slate-100 pb-4">
        <h2 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider font-heading flex items-center gap-2">
          <i class="ph-bold ph-list-bullets text-base text-gov-600"></i>
          <span>Rekam Jejak Log Aktivitas Terbaru</span>
        </h2>
        <span class="text-xs text-slate-400 font-mono">Total Log: {{ $activityLogs->total() }} Record</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-slate-50/50">
              <th class="py-3 px-3">Waktu Presisi</th>
              <th class="py-3 px-3">Pengguna / Email</th>
              <th class="py-3 px-3">Peran (Role)</th>
              <th class="py-3 px-3">Aksi Sistem</th>
              <th class="py-3 px-3">IP Address</th>
              <th class="py-3 px-3">Detail Deskripsi Audit</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($activityLogs as $log)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3.5 px-3 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                  {{ $log->created_at->format('d/m/Y H:i:s') }}
                </td>
                <td class="py-3.5 px-3 font-semibold text-slate-900">
                  {{ $log->user ? $log->user->name : ($log->email ?: 'Pengunjung / Guest') }}
                  <span class="block text-[10px] font-mono text-slate-400 font-normal">{{ $log->email }}</span>
                </td>
                <td class="py-3.5 px-3">
                  @if($log->role === 'admin_pupr')
                    <span class="px-2.5 py-0.5 bg-purple-100 text-purple-800 border border-purple-200 rounded-full text-[9px] font-extrabold uppercase">Admin PUPR</span>
                  @elseif($log->role === 'kontraktor')
                    <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 border border-blue-200 rounded-full text-[9px] font-extrabold uppercase">Kontraktor</span>
                  @elseif($log->role === 'pemeriksa_lapangan')
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full text-[9px] font-extrabold uppercase">Pengawas</span>
                  @else
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 rounded-full text-[9px] font-extrabold uppercase">Guest</span>
                  @endif
                </td>
                <td class="py-3.5 px-3">
                  @if(str_contains($log->action, 'SUCCESS'))
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold text-[10px]">{{ $log->action }}</span>
                  @elseif(str_contains($log->action, 'FAILED') || str_contains($log->action, 'BLOCKED'))
                    <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded font-bold text-[10px]">{{ $log->action }}</span>
                  @else
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-bold text-[10px]">{{ $log->action }}</span>
                  @endif
                </td>
                <td class="py-3.5 px-3 font-mono text-slate-800 font-bold">
                  {{ $log->ip_address }}
                </td>
                <td class="py-3.5 px-3 text-slate-600 max-w-[320px] truncate" title="{{ $log->description }}">
                  {{ $log->description }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-12 text-center text-slate-400 font-medium">Belum ada rekaman log aktivitas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination links -->
      <div class="pt-4 border-t border-slate-100">
        {{ $activityLogs->links() }}
      </div>
    </div>
  </div>

  <!-- MODE MOBILE NATIVE VIEW (<768px) -->
  <div class="block md:hidden min-h-screen bg-slate-900 text-slate-100 font-sans space-y-4 p-3 pb-24">
    
    <!-- MOBILE HEADER HERO CARD -->
    <div class="relative rounded overflow-hidden border border-slate-800 bg-slate-950 p-4 sm:p-5 shadow-xl min-h-[140px] flex flex-col justify-end">
      <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60" style="background-image: url('{{ asset('assets/contractor_hero.jpg') }}');"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/65 to-slate-950/30"></div>

      <div class="relative z-10 space-y-2">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-gov-900/80 border border-gov-750 text-emerald-300 text-[9px] font-bold tracking-wider uppercase backdrop-blur-sm">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
          <span>PEMERINTAH KABUPATEN BANJARNEGARA</span>
        </div>
        <h1 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider font-heading leading-tight drop-shadow-md">LOG AKTIVITAS PENGGUNA</h1>
        <p class="text-[10px] sm:text-xs text-slate-200 leading-normal drop-shadow">Mencatat riwayat login/logout, pengajuan progres, verifikasi, dan IP address pengguna.</p>
      </div>
    </div>

    <!-- LOG ENTRIES MOBILE CARDS -->
    <div class="space-y-3">
      @forelse($activityLogs as $log)
        <div class="bg-slate-950 border border-slate-800 p-3.5 rounded space-y-2 text-xs shadow-md">
          <div class="flex justify-between items-start">
            <span class="text-[10px] font-mono text-slate-400">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
            @if(str_contains($log->action, 'SUCCESS'))
              <span class="px-2 py-0.5 bg-emerald-950 text-emerald-300 border border-emerald-800 rounded text-[9px] font-extrabold uppercase">{{ $log->action }}</span>
            @elseif(str_contains($log->action, 'FAILED') || str_contains($log->action, 'BLOCKED'))
              <span class="px-2 py-0.5 bg-red-950 text-red-300 border border-red-800 rounded text-[9px] font-extrabold uppercase">{{ $log->action }}</span>
            @else
              <span class="px-2 py-0.5 bg-slate-900 text-slate-300 border border-slate-800 rounded text-[9px] font-extrabold uppercase">{{ $log->action }}</span>
            @endif
          </div>

          <div>
            <h4 class="font-extrabold text-white text-xs font-heading">{{ $log->user ? $log->user->name : ($log->email ?: 'Pengunjung / Guest') }}</h4>
            <span class="text-[10px] font-mono text-slate-400 block">{{ $log->email }}</span>
          </div>

          <div class="flex justify-between items-center text-[10px] pt-2 border-t border-slate-800/80">
            <span class="font-mono text-gov-400 font-bold">IP: {{ $log->ip_address }}</span>
            <span class="text-slate-400 uppercase text-[9px] font-bold">{{ $log->role ?: 'guest' }}</span>
          </div>
          <p class="text-[11px] text-slate-300 italic leading-snug">{{ $log->description }}</p>
        </div>
      @empty
        <div class="p-6 text-center text-slate-500 text-xs">Belum ada log aktivitas tercatat.</div>
      @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="pt-2">
      {{ $activityLogs->links() }}
    </div>
  </div>
@endsection
